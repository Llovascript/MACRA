from fastapi import APIRouter, HTTPException, Depends, status
from sqlalchemy.orm import Session
from datetime import timedelta
from database import get_db
from models import Usuario, Rol, EstatusG
from schemas import UsuarioCreate, UsuarioResponse, LoginRequest, Token
from utils import get_password_hash, authenticate_user, create_access_token, ACCESS_TOKEN_EXPIRE_MINUTES
from dependencies import get_current_active_user

router = APIRouter(prefix="/auth", tags=["autenticacion"])

@router.post("/register", response_model=UsuarioResponse)
def register_user(usuario: UsuarioCreate, db: Session = Depends(get_db)):
    # Check if rol exists
    rol = db.query(Rol).filter(Rol.id == usuario.rol_id).first()
    if not rol:
        raise HTTPException(status_code=400, detail="Rol not found")
    
    # Check if estatus exists (if provided)
    if usuario.estatus_id:
        estatus = db.query(EstatusG).filter(EstatusG.id == usuario.estatus_id).first()
        if not estatus:
            raise HTTPException(status_code=400, detail="Estatus not found")
    
    # Check if email already exists
    existing_user = db.query(Usuario).filter(Usuario.correo == usuario.correo).first()
    if existing_user:
        raise HTTPException(status_code=400, detail="Email already registered")
    
    # Hash password
    hashed_password = get_password_hash(usuario.contraseña)
    
    # Create user with hashed password
    usuario_dict = usuario.dict()
    usuario_dict["contraseña"] = hashed_password
    
    db_usuario = Usuario(**usuario_dict)
    db.add(db_usuario)
    try:
        db.commit()
        db.refresh(db_usuario)
        return db_usuario
    except Exception as e:
        db.rollback()
        raise HTTPException(status_code=400, detail="Error creating user")

@router.post("/login", response_model=Token)
async def login(login_data: LoginRequest, db: Session = Depends(get_db)):
    user = authenticate_user(db, login_data.correo, login_data.contraseña)
    if not user:
        raise HTTPException(
            status_code=status.HTTP_401_UNAUTHORIZED,
            detail="Incorrect email or password",
            headers={"WWW-Authenticate": "Bearer"},
        )
    if not user.aprobacion:
        raise HTTPException(
            status_code=status.HTTP_401_UNAUTHORIZED,
            detail="User account not approved",
        )
    
    access_token_expires = timedelta(minutes=ACCESS_TOKEN_EXPIRE_MINUTES)
    access_token = create_access_token(
        data={"sub": str(user.id)}, expires_delta=access_token_expires
    )
    return {"access_token": access_token, "token_type": "bearer"}

@router.get("/me", response_model=UsuarioResponse)
async def read_users_me(current_user: Usuario = Depends(get_current_active_user)):
    return current_user
