from passlib.context import CryptContext
from jose import JWTError, jwt
from datetime import datetime, timedelta
from typing import Optional
from sqlalchemy.orm import Session
from models import Usuario
import bcrypt
import logging

# Configurar logging
logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

# No modificar el token
SECRET_KEY = "e2ef419bd6082ba65a86e8e9c04e62665e86e56af53bdcd15a0d3a68e1f11723"
ALGORITHM = "HS256"
ACCESS_TOKEN_EXPIRE_MINUTES = 30

pwd_context = CryptContext(schemes=["bcrypt"], deprecated="auto")

def verify_password(plain_password: str, hashed_password: str) -> bool:
    """
    Verificar contraseña - compatible con bcrypt de Laravel y FastAPI
    """
    try:
        logger.info(f"Verificando contraseña. Hash type: {hashed_password[:10]}...")
        
        # Primero intentar con el contexto de passlib (FastAPI)
        if pwd_context.verify(plain_password, hashed_password):
            logger.info("Contraseña verificada con passlib")
            return True
    except Exception as e:
        logger.warning(f"Error con passlib: {e}")
    
    try:
        # Si falla, intentar con bcrypt directo (Laravel)
        if hashed_password.startswith('$2b$') or hashed_password.startswith('$2y$'):
            # Convertir $2y$ a $2b$ si es necesario (compatibilidad Laravel)
            hash_to_check = hashed_password
            if hashed_password.startswith('$2y$'):
                hash_to_check = '$2b$' + hashed_password[4:]
                logger.info("Convertido de $2y$ a $2b$")
            
            # Verificar con bcrypt directo
            result = bcrypt.checkpw(plain_password.encode('utf-8'), hash_to_check.encode('utf-8'))
            if result:
                logger.info("Contraseña verificada con bcrypt directo")
            else:
                logger.warning("Contraseña no coincide con bcrypt directo")
            return result
    except Exception as e:
        logger.error(f"Error verificando contraseña con bcrypt: {e}")
        return False
    
    logger.warning("No se pudo verificar la contraseña con ningún método")
    return False

def get_password_hash(password: str) -> str:
    """
    Hashear contraseña usando bcrypt (compatible con Laravel)
    """
    return pwd_context.hash(password)

def authenticate_user(db: Session, email: str, password: str):
    """
    Autenticar usuario con verificación de contraseña mejorada
    """
    logger.info(f"Intentando autenticar usuario: {email}")
    
    user = db.query(Usuario).filter(
        Usuario.correo == email, 
        Usuario.del_flag == False
    ).first()
    
    if not user:
        logger.warning(f"Usuario no encontrado: {email}")
        return False
    
    logger.info(f"Usuario encontrado. ID: {user.id}, Hash: {user.contraseña[:20]}...")
    
    if not verify_password(password, user.contraseña):
        logger.warning(f"Contraseña incorrecta para usuario: {email}")
        return False
    
    logger.info(f"Autenticación exitosa para usuario: {email}")
    return user

def create_access_token(data: dict, expires_delta: Optional[timedelta] = None):
    to_encode = data.copy()
    if expires_delta:
        expire = datetime.utcnow() + expires_delta
    else:
        expire = datetime.utcnow() + timedelta(minutes=15)
    to_encode.update({"exp": expire})
    encoded_jwt = jwt.encode(to_encode, SECRET_KEY, algorithm=ALGORITHM)
    return encoded_jwt
