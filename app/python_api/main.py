
cat <<'EOF' > python_api/main.py
from fastapi import FastAPI, Depends, HTTPException, Body
from fastapi.security import HTTPBearer, HTTPAuthorizationCredentials
from pydantic import BaseModel
from pydantic_settings import BaseSettings
from sqlalchemy.ext.asyncio import create_async_engine, async_sessionmaker
from sqlalchemy import text
from urllib.parse import quote_plus
import hashlib
import random

# --- CONFIGURAÇÃO ---
class Settings(BaseSettings):
    db_user: str = "makis_ead_user"
    db_password: str = "admin@123456"
    db_host: str = "db"
    db_port: int = 3306
    db_name: str = "makis_ead_db"

    @property
    def database_url(self) -> str:
        encoded_password = quote_plus(self.db_password)
        return f"mysql+aiomysql://{self.db_user}:{encoded_password}@{self.db_host}:{self.db_port}/{self.db_name}"

settings = Settings()

engine = create_async_engine(settings.database_url, echo=False)
AsyncSessionLocal = async_sessionmaker(engine, expire_on_commit=False)

app = FastAPI(title="Makis EAD - Gamification Engine")
security = HTTPBearer()

class StudentAnswer(BaseModel):
    lesson_id: int
    user_answer: str

# --- SEGURANÇA ---
async def get_current_user(credentials: HTTPAuthorizationCredentials = Depends(security)):
    token_full = credentials.credentials
    if '|' not in token_full:
        raise HTTPException(status_code=401, detail="Token inválido")
    
    token_id, token_string = token_full.split('|', 1)
    hashed_token = hashlib.sha256(token_string.encode()).hexdigest()

    async with AsyncSessionLocal() as session:
        query = text("SELECT tokenable_id FROM personal_access_tokens WHERE id = :id AND token = :hashed_token")
        result = await session.execute(query, {"id": token_id, "hashed_token": hashed_token})
        user_id = result.scalar()

        if not user_id:
            raise HTTPException(status_code=401, detail="Não autorizado")
            
        user_query = text("SELECT id, name, email FROM users WHERE id = :uid")
        user_result = await session.execute(user_query, {"uid": user_id})
        return user_result.mappings().first()

# --- ROTAS ---

@app.get("/")
async def root():
    return {"engine": "Makis EAD", "status": "saving_progress_active"}

@app.get("/dashboard/stats")
async def get_dashboard_stats(user: dict = Depends(get_current_user)):
    async with AsyncSessionLocal() as session:
        total_users = (await session.execute(text("SELECT COUNT(*) FROM users"))).scalar()
        
        # SOMA REAL DE XP
        xp_query = text("SELECT SUM(xp_earned) FROM user_progress")
        total_xp_result = await session.execute(xp_query)
        total_xp = total_xp_result.scalar() or 0
        
    return {
        "kpis": {
            "total_students": total_users,
            "engagement_score": "Top 10%", 
            "total_platform_xp": int(total_xp)
        }
    }

@app.post("/learn/check-answer")
async def check_answer(
    attempt: StudentAnswer, 
    user: dict = Depends(get_current_user)
):
    cleaned_answer = attempt.user_answer.strip().lower()
    
    # Validação
    is_correct = "print" in cleaned_answer and "ola mundo" in cleaned_answer
    
    xp = 0
    feedback = "Tente novamente."
    
    if is_correct:
        base_xp = 10
        bonus = random.choice([0, 5])
        xp = base_xp + bonus
        feedback = "Resposta Correta! Progresso Salvo."

        # GRAVAR NO BANCO
        async with AsyncSessionLocal() as session:
            insert_query = text("""
                INSERT INTO user_progress (user_id, lesson_id, xp_earned, is_completed, created_at, updated_at)
                VALUES (:uid, :lid, :xp, 1, NOW(), NOW())
            """)
            await session.execute(insert_query, {
                "uid": user['id'],
                "lid": attempt.lesson_id,
                "xp": xp
            })
            await session.commit()
            
    return {
        "user": user['name'],
        "correct": is_correct,
        "xp_earned": xp,
        "feedback": feedback,
        "saved_to_db": is_correct  # <--- Procure por este campo na resposta
    }
EOF