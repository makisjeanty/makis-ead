"""
Webhook Simulator for MercadoPago and Stripe
Simulates webhook calls with load testing capabilities
"""

from fastapi import FastAPI, BackgroundTasks, HTTPException
from pydantic import BaseModel, Field
from typing import List, Optional, Literal
import httpx
import asyncio
from datetime import datetime
import random
import uuid
from concurrent.futures import ThreadPoolExecutor
import time
import statistics

app = FastAPI(
    title="Payment Gateway Webhook Simulator",
    description="Simulates MercadoPago and Stripe webhooks with load testing",
    version="1.0.0"
)

# Configuration
class WebhookConfig(BaseModel):
    target_url: str = Field(..., description="Target webhook URL")
    gateway: Literal["mercadopago", "stripe"] = Field(..., description="Payment gateway")
    delay_ms: int = Field(default=0, description="Delay between requests in milliseconds")

class LoadTestConfig(BaseModel):
    target_url: str
    gateway: Literal["mercadopago", "stripe"]
    total_requests: int = Field(default=100, ge=1, le=10000)
    concurrent_requests: int = Field(default=10, ge=1, le=100)
    delay_ms: int = Field(default=0, ge=0, le=5000)

class WebhookStats(BaseModel):
    total_sent: int = 0
    successful: int = 0
    failed: int = 0
    duplicate_responses: int = 0
    avg_response_time_ms: float = 0
    min_response_time_ms: float = 0
    max_response_time_ms: float = 0
    errors: List[str] = []

# In-memory storage for test results
test_results = {}

def generate_mercadopago_webhook(payment_id: Optional[str] = None) -> dict:
    """Generate a realistic MercadoPago webhook payload"""
    if not payment_id:
        payment_id = str(random.randint(1000000000, 9999999999))
    
    return {
        "action": "payment.created",
        "api_version": "v1",
        "data": {
            "id": payment_id
        },
        "date_created": datetime.utcnow().isoformat() + "Z",
        "id": random.randint(1000000000, 9999999999),
        "live_mode": False,
        "type": "payment",
        "user_id": str(random.randint(100000000, 999999999))
    }

def generate_stripe_webhook(payment_id: Optional[str] = None) -> dict:
    """Generate a realistic Stripe webhook payload"""
    if not payment_id:
        payment_id = str(random.randint(1, 99999))
    
    event_id = f"evt_{uuid.uuid4().hex[:24]}"
    
    return {
        "id": event_id,
        "object": "event",
        "api_version": "2023-10-16",
        "created": int(time.time()),
        "data": {
            "object": {
                "id": f"cs_{uuid.uuid4().hex[:24]}",
                "object": "checkout.session",
                "amount_total": random.randint(1000, 50000),
                "currency": "usd",
                "customer": f"cus_{uuid.uuid4().hex[:14]}",
                "metadata": {
                    "payment_id": payment_id
                },
                "payment_status": "paid",
                "status": "complete"
            }
        },
        "livemode": False,
        "pending_webhooks": 1,
        "type": "checkout.session.completed"
    }

async def send_webhook(
    url: str,
    gateway: str,
    payload: dict,
    headers: Optional[dict] = None
) -> tuple[int, float, dict]:
    """Send a single webhook and measure response time"""
    start_time = time.time()
    
    async with httpx.AsyncClient(timeout=30.0) as client:
        try:
            response = await client.post(url, json=payload, headers=headers or {})
            elapsed_ms = (time.time() - start_time) * 1000
            
            return response.status_code, elapsed_ms, response.json() if response.text else {}
        except Exception as e:
            elapsed_ms = (time.time() - start_time) * 1000
            raise HTTPException(status_code=500, detail=str(e))

@app.post("/simulate/single")
async def simulate_single_webhook(config: WebhookConfig):
    """Simulate a single webhook call"""
    
    if config.gateway == "mercadopago":
        payload = generate_mercadopago_webhook()
        headers = {"Content-Type": "application/json"}
    else:
        payload = generate_stripe_webhook()
        headers = {
            "Content-Type": "application/json",
            "Stripe-Signature": "t=1234567890,v1=mock_signature_for_testing"
        }
    
    try:
        status_code, response_time, response_data = await send_webhook(
            config.target_url,
            config.gateway,
            payload,
            headers
        )
        
        return {
            "success": True,
            "gateway": config.gateway,
            "status_code": status_code,
            "response_time_ms": round(response_time, 2),
            "payload": payload,
            "response": response_data
        }
    except Exception as e:
        return {
            "success": False,
            "gateway": config.gateway,
            "error": str(e),
            "payload": payload
        }

@app.post("/simulate/load-test")
async def simulate_load_test(config: LoadTestConfig, background_tasks: BackgroundTasks):
    """Start a load test with multiple concurrent webhook requests"""
    
    test_id = str(uuid.uuid4())
    
    test_results[test_id] = {
        "status": "running",
        "config": config.dict(),
        "started_at": datetime.utcnow().isoformat(),
        "stats": WebhookStats().dict()
    }
    
    background_tasks.add_task(run_load_test, test_id, config)
    
    return {
        "test_id": test_id,
        "status": "started",
        "message": f"Load test started with {config.total_requests} requests",
        "check_status_url": f"/test-results/{test_id}"
    }

async def run_load_test(test_id: str, config: LoadTestConfig):
    """Execute the load test"""
    stats = WebhookStats()
    response_times = []
    
    # Generate all payloads upfront
    payloads = []
    headers_list = []
    
    for i in range(config.total_requests):
        if config.gateway == "mercadopago":
            payload = generate_mercadopago_webhook()
            headers = {"Content-Type": "application/json"}
        else:
            payload = generate_stripe_webhook()
            headers = {
                "Content-Type": "application/json",
                "Stripe-Signature": f"t={int(time.time())},v1=test_sig_{uuid.uuid4().hex[:16]}"
            }
        
        payloads.append(payload)
        headers_list.append(headers)
    
    # Execute requests in batches
    semaphore = asyncio.Semaphore(config.concurrent_requests)
    
    async def send_with_semaphore(idx: int):
        async with semaphore:
            try:
                status_code, elapsed_ms, response_data = await send_webhook(
                    config.target_url,
                    config.gateway,
                    payloads[idx],
                    headers_list[idx]
                )
                
                response_times.append(elapsed_ms)
                stats.successful += 1
                
                if response_data.get('status') == 'duplicate':
                    stats.duplicate_responses += 1
                
                if config.delay_ms > 0:
                    await asyncio.sleep(config.delay_ms / 1000)
                
            except Exception as e:
                stats.failed += 1
                if len(stats.errors) < 10:
                    stats.errors.append(f"Request {idx + 1}: {str(e)}")
    
    tasks = [send_with_semaphore(i) for i in range(config.total_requests)]
    await asyncio.gather(*tasks, return_exceptions=True)
    
    # Calculate statistics
    stats.total_sent = config.total_requests
    
    if response_times:
        stats.avg_response_time_ms = round(statistics.mean(response_times), 2)
        stats.min_response_time_ms = round(min(response_times), 2)
        stats.max_response_time_ms = round(max(response_times), 2)
    
    test_results[test_id]["status"] = "completed"
    test_results[test_id]["completed_at"] = datetime.utcnow().isoformat()
    test_results[test_id]["stats"] = stats.dict()

@app.get("/test-results/{test_id}")
async def get_test_results(test_id: str):
    """Get load test results"""
    if test_id not in test_results:
        raise HTTPException(status_code=404, detail="Test not found")
    
    return test_results[test_id]

@app.post("/simulate/duplicate-test")
async def simulate_duplicate_test(
    target_url: str,
    gateway: Literal["mercadopago", "stripe"],
    duplicate_count: int = Field(default=5, ge=2, le=20)
):
    """Test idempotency by sending the same webhook multiple times"""
    
    if gateway == "mercadopago":
        payload = generate_mercadopago_webhook("12345678")
        headers = {"Content-Type": "application/json"}
    else:
        payload = generate_stripe_webhook("999")
        headers = {
            "Content-Type": "application/json",
            "Stripe-Signature": "t=1234567890,v1=same_signature_for_all"
        }
    
    results = []
    
    for i in range(duplicate_count):
        try:
            status_code, response_time, response_data = await send_webhook(
                target_url,
                gateway,
                payload,
                headers
            )
            
            results.append({
                "attempt": i + 1,
                "status_code": status_code,
                "response_time_ms": round(response_time, 2),
                "response": response_data
            })
            
            await asyncio.sleep(0.1)
            
        except Exception as e:
            results.append({
                "attempt": i + 1,
                "error": str(e)
            })
    
    return {
        "test": "duplicate_idempotency",
        "gateway": gateway,
        "payload": payload,
        "results": results,
        "summary": {
            "total_attempts": duplicate_count,
            "duplicate_detected": sum(1 for r in results if r.get('response', {}).get('status') == 'duplicate')
        }
    }

@app.get("/")
async def root():
    """API Information"""
    return {
        "name": "Payment Gateway Webhook Simulator",
        "version": "1.0.0",
        "endpoints": {
            "single_webhook": "/simulate/single",
            "load_test": "/simulate/load-test",
            "duplicate_test": "/simulate/duplicate-test",
            "test_results": "/test-results/{test_id}"
        },
        "supported_gateways": ["mercadopago", "stripe"]
    }

if __name__ == "__main__":
    import uvicorn
    uvicorn.run(app, host="0.0.0.0", port=8000)
