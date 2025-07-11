from fastapi import HTTPException, Depends, status
from fastapi.security import HTTPBearer, HTTPAuthorizationCredentials
from jose import JWTError, jwt
from sqlalchemy.orm import Session
from typing import List
from database import SessionLocal, get_db
from models import Usuario, Rol
from schemas import TokenData
from utils import SECRET_KEY, ALGORITHM

security = HTTPBearer()

async def get_current_user(credentials: HTTPAuthorizationCredentials = Depends(security), db: Session = Depends(get_db)):
    credentials_exception = HTTPException(
        status_code=status.HTTP_401_UNAUTHORIZED,
        detail="Could not validate credentials",
        headers={"WWW-Authenticate": "Bearer"},
    )
    try:
        token = credentials.credentials
        payload = jwt.decode(token, SECRET_KEY, algorithms=[ALGORITHM])
        user_id: int = payload.get("sub")
        if user_id is None:
            raise credentials_exception
        token_data = TokenData(user_id=user_id)
    except JWTError:
        raise credentials_exception
    
    user = db.query(Usuario).filter(
        Usuario.id == token_data.user_id,
        Usuario.del_flag == False
    ).first()
    if user is None:
        raise credentials_exception
    return user

async def get_current_active_user(current_user: Usuario = Depends(get_current_user)):
    if not current_user.aprobacion:
        raise HTTPException(status_code=400, detail="Inactive user")
    return current_user

def require_role(required_roles: List[str]):
    def role_checker(current_user: Usuario = Depends(get_current_active_user)):
        db = SessionLocal()
        try:
            user_role = db.query(Rol).filter(Rol.id == current_user.rol_id).first()
            if not user_role or user_role.nombre not in required_roles:
                raise HTTPException(
                    status_code=status.HTTP_403_FORBIDDEN,
                    detail="Not enough permissions"
                )
            return current_user
        finally:
            db.close()
    return role_checker
