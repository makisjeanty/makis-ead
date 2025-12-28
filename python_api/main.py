from fastapi import FastAPI, Depends, HTTPException, Body
from fastapi.security import HTTPBearer, HTTPAuthorizationCredentials
from pydantic import BaseModel
from pydantic_settings import BaseSettings
from sqlalchemy.ext.asyncio import create_async_engine, async_sessionmaker
from sqlalchemy import text
from urllib.parse import quote_plus
import hashlib
import random # Para simular variações de XP

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

# Modelo de dados para receber a resposta do aluno
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
    return {"engine": "Mimo-Style Logic", "status": "ready"}

@app.get("/dashboard/stats")
async def get_dashboard_stats(user: dict = Depends(get_current_user)):
    # Mantendo a rota do dashboard que já criamos
    async with AsyncSessionLocal() as session:
        total_users = (await session.execute(text("SELECT COUNT(*) FROM users"))).scalar()
        # Simulação de XP Total da plataforma
        total_xp = total_users * 150 
        
    return {
        "kpis": {
            "total_students": total_users,
            "engagement_score": f"{random.randint(40, 90)}%", 
            "total_platform_xp": total_xp
        }
    }

# --- NOVA ROTA: VALIDAÇÃO DE EXERCÍCIO ---
@app.post("/learn/check-answer")
async def check_answer(
    attempt: StudentAnswer, 
    user: dict = Depends(get_current_user)
):
    """
    Simula a correção de um exercício estilo Mimo.
    Exercício: "Imprima 'Ola Mundo' em Python"
    """
    
    # 1. Normaliza a resposta (remove espaços extras, poe em minúscula)
    cleaned_answer = attempt.user_answer.strip().lower()
    
    # 2. Lógica de Correção (Simulada para Python básico)
    # A resposta correta deve conter 'print' e 'ola mundo'
    is_correct = "print" in cleaned_answer and "ola mundo" in cleaned_answer
    
    response_data = {
        "user": user['name'],
        "correct": is_correct,
        "xp_earned": 0,
        "feedback": "",
        "streak_bonus": False
    }

    if is_correct:
        # Se acertou, o Python calcula o XP
        base_xp = 10
        bonus = random.choice([0, 5]) # Sorte de bônus
        
        response_data["xp_earned"] = base_xp + bonus
        response_data["feedback"] = "Mandou bem! Código correto."
        response_data["streak_bonus"] = (bonus > 0)
    else:
        # Se errou, o Python dá uma dica (Inteligência)
        response_data["feedback"] = "Quase lá! Lembre-se de usar a função print() e as aspas."

    return response_data
