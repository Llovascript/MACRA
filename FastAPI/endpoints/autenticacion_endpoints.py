from fastapi import APIRouter, HTTPException, Depends, status
from sqlalchemy.orm import Session
from datetime import timedelta
from database import get_db
from models import Usuario, Rol, EstatusG
from schemas import UsuarioCreate, UsuarioResponse, LoginRequest, Token
from utils import get_password_hash, authenticate_user, create_access_token, ACCESS_TOKEN_EXPIRE_MINUTES
from dependencies import get_current_active_user
import logging


logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

router = APIRouter(prefix="/auth", tags=["autenticacion"])

@router.post("/register", response_model=UsuarioResponse)
def register_user(usuario: UsuarioCreate, db: Session = Depends(get_db)):
    try:
        logger.info(f"Intento de registro para: {usuario.correo}")
        
        rol = db.query(Rol).filter(Rol.id == usuario.rol_id).first()
        if not rol:
            logger.error(f"Rol {usuario.rol_id} no encontrado")
            raise HTTPException(status_code=400, detail="Rol not found")
            
        if usuario.estatus_id:
            estatus = db.query(EstatusG).filter(EstatusG.id == usuario.estatus_id).first()
            if not estatus:
                logger.error(f"Estatus {usuario.estatus_id} no encontrado")
                raise HTTPException(status_code=400, detail="Estatus not found")
        
        existing_user = db.query(Usuario).filter(Usuario.correo == usuario.correo).first()
        if existing_user:
            logger.error(f"Email {usuario.correo} ya está registrado")
            raise HTTPException(status_code=400, detail="Email already registered")
            
        hashed_password = get_password_hash(usuario.contraseña)
        logger.info("Contraseña hasheada exitosamente")
        
        usuario_dict = usuario.dict()
        usuario_dict["contraseña"] = hashed_password
        
        if not usuario_dict.get("estatus_id"):
            usuario_dict["estatus_id"] = 1
        if not usuario_dict.get("aprobacion"):
            usuario_dict["aprobacion"] = True  # Auto-aprobar por ahora(cambiar despues a false)
        if not usuario_dict.get("del_flag"):
            usuario_dict["del_flag"] = False
            
        db_usuario = Usuario(**usuario_dict)
        db.add(db_usuario)
        db.commit()
        db.refresh(db_usuario)
        
        logger.info(f"Usuario {usuario.correo} registrado exitosamente")
        return db_usuario
        
    except HTTPException:
        raise
    except Exception as e:
        db.rollback()
        logger.error(f"Error creando usuario: {str(e)}")
        raise HTTPException(status_code=400, detail="Error creando usuario")

@router.post("/login", response_model=Token)
async def login(login_data: LoginRequest, db: Session = Depends(get_db)):
    try:
        logger.info(f"=== INICIO LOGIN PARA: {login_data.correo} ===")
        
        user = db.query(Usuario).filter(Usuario.correo == login_data.correo).first()
        if not user:
            logger.error(f"Usuario {login_data.correo} no encontrado en la base de datos")
            raise HTTPException(
                status_code=status.HTTP_401_UNAUTHORIZED,
                detail="Incorrect email or password",
                headers={"WWW-Authenticate": "Bearer"},
            )
        
        logger.info(f"Usuario encontrado - ID: {user.id}, Aprobación: {user.aprobacion}, Del_flag: {user.del_flag}")
        logger.info(f"Hash en BD: {user.contraseña}")
        
        authenticated_user = authenticate_user(db, login_data.correo, login_data.contraseña)
        if not authenticated_user:
            logger.error(f"Autenticación fallida para {login_data.correo}")
            raise HTTPException(
                status_code=status.HTTP_401_UNAUTHORIZED,
                detail="Incorrect email or password",
                headers={"WWW-Authenticate": "Bearer"},
            )
        
        if not user.aprobacion:
            logger.error(f"Usuario {login_data.correo} no aprobado")
            raise HTTPException(
                status_code=status.HTTP_401_UNAUTHORIZED,
                detail="User account not approved",
            )
            
        access_token_expires = timedelta(minutes=ACCESS_TOKEN_EXPIRE_MINUTES)
        access_token = create_access_token(
            data={"sub": str(user.id)}, expires_delta=access_token_expires
        )
        
        logger.info(f"=== LOGIN EXITOSO PARA: {login_data.correo} ===")
        return {"access_token": access_token, "token_type": "bearer"}
        
    except HTTPException:
        raise
    except Exception as e:
        logger.error(f"Error inesperado en login: {str(e)}")
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail="Internal server error"
        )

@router.get("/me", response_model=UsuarioResponse)
async def read_users_me(current_user: Usuario = Depends(get_current_active_user)):
    return current_user

# Endpoint para debug - REMOVER EN PRODUCCIÓN
@router.get("/debug/users")
async def debug_users(db: Session = Depends(get_db)):
    """Endpoint para debug - ver usuarios en la base de datos"""
    users = db.query(Usuario).limit(5).all()
    return [
        {
            "id": u.id, 
            "correo": u.correo, 
            "hash_preview": u.contraseña[:30] + "...",
            "aprobacion": u.aprobacion,
            "del_flag": u.del_flag
        } 
        for u in users
    ]

@router.post("/debug/verify-password")
async def debug_verify_password(email: str, password: str, db: Session = Depends(get_db)):
    """Endpoint para debug - probar verificación de contraseña"""
    from utils import verify_password
    
    user = db.query(Usuario).filter(Usuario.correo == email).first()
    if not user:
        return {"error": "Usuario no encontrado"}
    
    result = verify_password(password, user.contraseña)
    return {
        "email": email,
        "hash_in_db": user.contraseña,
        "password_matches": result
    }
