from fastapi import APIRouter, HTTPException, Depends, status
from sqlalchemy.orm import Session
from typing import List
from database import get_db
from models import Usuario, Rol, EstatusG
from schemas import UsuarioCreate, UsuarioUpdate, UsuarioResponse
from utils import get_password_hash
from dependencies import require_role, get_current_active_user

router = APIRouter(prefix="/usuarios", tags=["usuarios"])

@router.post("/", response_model=UsuarioResponse)
def create_usuario(
    usuario: UsuarioCreate, 
    db: Session = Depends(get_db),
    current_user: Usuario = Depends(require_role(["admin"]))
):
    rol = db.query(Rol).filter(Rol.id == usuario.rol_id).first()
    if not rol:
        raise HTTPException(status_code=400, detail="Rol not found")

    if usuario.estatus_id:
        estatus = db.query(EstatusG).filter(EstatusG.id == usuario.estatus_id).first()
        if not estatus:
            raise HTTPException(status_code=400, detail="Estatus not found")
    
    existing_user = db.query(Usuario).filter(Usuario.correo == usuario.correo).first()
    if existing_user:
        raise HTTPException(status_code=400, detail="Email already registered")
    
    hashed_password = get_password_hash(usuario.contraseña)
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

@router.get("/", response_model=List[UsuarioResponse])
def get_usuarios(
    skip: int = 0, 
    limit: int = 100, 
    db: Session = Depends(get_db),
    current_user: Usuario = Depends(require_role(["admin"]))
):
    return db.query(Usuario).filter(Usuario.del_flag == False).offset(skip).limit(limit).all()

@router.get("/{usuario_id}", response_model=UsuarioResponse)
def get_usuario(
    usuario_id: int, 
    db: Session = Depends(get_db),
    current_user: Usuario = Depends(get_current_active_user)
):  
    
    usuario = db.query(Usuario).filter(Usuario.id == usuario_id, Usuario.del_flag == False).first()
    if not usuario:
        raise HTTPException(status_code=404, detail="Usuario not found")
    if current_user.rol.nombre == "admin":
        return usuario
    elif current_user.id == usuario_id:
        return usuario
    else:
        raise HTTPException(
            status_code=status.HTTP_403_FORBIDDEN,
            detail="Not enough permissions to access this user"
        )

@router.put("/{usuario_id}", response_model=UsuarioResponse)
def update_usuario(
    usuario_id: int, 
    usuario_update: UsuarioUpdate, 
    db: Session = Depends(get_db),
    current_user: Usuario = Depends(get_current_active_user)
):
    db_user = db.query(Usuario).filter(Usuario.id == current_user.id).first()
    user_role = db.query(Rol).filter(Rol.id == db_user.rol_id).first()
    
    if current_user.id != usuario_id and user_role.nombre not in ["admin", "moderador"]:
        raise HTTPException(
            status_code=status.HTTP_403_FORBIDDEN,
            detail="Not enough permissions to update this user"
        )
    
    usuario = db.query(Usuario).filter(Usuario.id == usuario_id, Usuario.del_flag == False).first()
    if not usuario:
        raise HTTPException(status_code=404, detail="Usuario not found")
    
    update_data = usuario_update.dict(exclude_unset=True)
    if "contraseña" in update_data:
        update_data["contraseña"] = get_password_hash(update_data["contraseña"])
    
    for field, value in update_data.items():
        setattr(usuario, field, value)
    
    try:
        db.commit()
        db.refresh(usuario)
        return usuario
    except Exception as e:
        db.rollback()
        raise HTTPException(status_code=400, detail="Error updating user")

@router.delete("/{usuario_id}")
def delete_usuario(
    usuario_id: int, 
    db: Session = Depends(get_db),
    current_user: Usuario = Depends(require_role(["admin"]))
):
    usuario = db.query(Usuario).filter(Usuario.id == usuario_id, Usuario.del_flag == False).first()
    if not usuario:
        raise HTTPException(status_code=404, detail="Usuario not found")
    
    usuario.del_flag = True
    try:
        db.commit()
        return {"message": "Usuario deleted successfully"}
    except Exception as e:
        db.rollback()
        raise HTTPException(status_code=400, detail="Error deleting user")

@router.put("/{usuario_id}/aprobar", response_model=UsuarioResponse)
def approve_user(
    usuario_id: int,
    aprobacion: bool,
    db: Session = Depends(get_db),
    current_user: Usuario = Depends(require_role(["admin"]))
):
    usuario = db.query(Usuario).filter(
        Usuario.id == usuario_id,
        Usuario.del_flag == False
    ).first()
    
    if not usuario:
        raise HTTPException(status_code=404, detail="Usuario no encontrado")
    
    usuario.aprobacion = aprobacion
    try:
        db.commit()
        db.refresh(usuario)
        return usuario
    except Exception as e:
        db.rollback()
        raise HTTPException(status_code=400, detail="Error actualizando aprobación de usuario")