from fastapi import APIRouter, Depends, HTTPException, Query, Header
from sqlalchemy.orm import Session
from typing import List, Optional
from database import get_db
from models import Usuario
from schemas import UsuarioResponse, UsuarioUpdate
from utils import get_password_hash
import logging

router = APIRouter(prefix="/admin", tags=["admin"])

# Clave secreta para autenticación entre servicios
ADMIN_SECRET_KEY = "admin-secret-2024"  # Cambiar por una clave segura

def verify_admin_key(x_admin_key: str = Header(None)):
    """Verificar que la petición viene de Laravel admin"""
    if x_admin_key != ADMIN_SECRET_KEY:
        raise HTTPException(status_code=401, detail="Unauthorized admin access")
    return True

@router.get("/usuarios/search", response_model=List[UsuarioResponse])
def search_usuarios(
    db: Session = Depends(get_db),
    admin_verified: bool = Depends(verify_admin_key),
    rol_id: Optional[int] = Query(None, description="Filtrar por rol (2=donante, 3=beneficiario)"),
    search: Optional[str] = Query(None, description="Buscar por nombre, email o teléfono"),
    limit: Optional[int] = Query(50, description="Límite de resultados"),
    skip: Optional[int] = Query(0, description="Saltar registros")
):
    """
    Buscar usuarios para el panel de administración de Laravel
    """
    try:
        query = db.query(Usuario).filter(Usuario.del_flag == False)
        
        # Filtrar por rol si se especifica
        if rol_id:
            query = query.filter(Usuario.rol_id == rol_id)
        
        # Buscar por término si se especifica
        if search and len(search.strip()) >= 2:
            search_term = f"%{search.strip()}%"
            query = query.filter(
                (Usuario.nombre.ilike(search_term)) |
                (Usuario.correo.ilike(search_term)) |
                (Usuario.telefono.ilike(search_term)) |
                (Usuario.aP.ilike(search_term)) |
                (Usuario.aM.ilike(search_term))
            )
        
        # Aplicar límite y offset
        usuarios = query.offset(skip).limit(limit).all()
        
        logging.info(f"Admin search: encontrados {len(usuarios)} usuarios con rol_id={rol_id}, search='{search}'")
        
        return usuarios
        
    except Exception as e:
        logging.error(f"Error en búsqueda admin: {str(e)}")
        raise HTTPException(status_code=500, detail=f"Error interno: {str(e)}")

@router.get("/usuarios/{usuario_id}", response_model=UsuarioResponse)
def get_usuario_admin(
    usuario_id: int, 
    db: Session = Depends(get_db),
    admin_verified: bool = Depends(verify_admin_key)
):
    """
    Obtener un usuario específico para el admin de Laravel
    """
    try:
        usuario = db.query(Usuario).filter(
            Usuario.id == usuario_id,
            Usuario.del_flag == False
        ).first()
        
        if not usuario:
            raise HTTPException(status_code=404, detail="Usuario no encontrado")
        
        return usuario
        
    except HTTPException:
        raise
    except Exception as e:
        logging.error(f"Error al obtener usuario {usuario_id} (admin): {str(e)}")
        raise HTTPException(status_code=500, detail=f"Error interno: {str(e)}")

@router.put("/usuarios/{usuario_id}", response_model=UsuarioResponse)
def update_usuario_admin(
    usuario_id: int, 
    usuario_update: UsuarioUpdate, 
    db: Session = Depends(get_db),
    admin_verified: bool = Depends(verify_admin_key)
):
    """
    Actualizar un usuario desde el admin de Laravel
    """
    try:
        usuario = db.query(Usuario).filter(
            Usuario.id == usuario_id, 
            Usuario.del_flag == False
        ).first()
        
        if not usuario:
            raise HTTPException(status_code=404, detail="Usuario no encontrado")
        
        # Actualizar solo los campos proporcionados
        update_data = usuario_update.dict(exclude_unset=True)
        
        # Encriptar contraseña si se proporciona
        if "contraseña" in update_data and update_data["contraseña"]:
            update_data["contraseña"] = get_password_hash(update_data["contraseña"])
        
        for field, value in update_data.items():
            if value is not None:  # Solo actualizar campos con valor
                setattr(usuario, field, value)
        
        db.commit()
        db.refresh(usuario)
        
        logging.info(f"Usuario {usuario_id} actualizado por admin Laravel")
        return usuario
        
    except HTTPException:
        raise
    except Exception as e:
        db.rollback()
        logging.error(f"Error al actualizar usuario {usuario_id} (admin): {str(e)}")
        raise HTTPException(status_code=500, detail=f"Error interno: {str(e)}")

@router.delete("/usuarios/{usuario_id}")
def delete_usuario_admin(
    usuario_id: int, 
    db: Session = Depends(get_db),
    admin_verified: bool = Depends(verify_admin_key)
):
    """
    Eliminar un usuario desde el admin de Laravel (soft delete)
    """
    try:
        usuario = db.query(Usuario).filter(
            Usuario.id == usuario_id, 
            Usuario.del_flag == False
        ).first()
        
        if not usuario:
            raise HTTPException(status_code=404, detail="Usuario no encontrado")
        
        # Soft delete
        usuario.del_flag = True
        db.commit()
        
        logging.info(f"Usuario {usuario_id} eliminado por admin Laravel")
        return {"message": "Usuario eliminado exitosamente"}
        
    except HTTPException:
        raise
    except Exception as e:
        db.rollback()
        logging.error(f"Error al eliminar usuario {usuario_id} (admin): {str(e)}")
        raise HTTPException(status_code=500, detail=f"Error interno: {str(e)}")

@router.get("/estadisticas")
def get_estadisticas(
    db: Session = Depends(get_db),
    admin_verified: bool = Depends(verify_admin_key)
):
    """
    Obtener estadísticas para el dashboard del admin
    """
    try:
        # Contar usuarios por rol
        total_usuarios = db.query(Usuario).filter(Usuario.del_flag == False).count()
        total_beneficiarios = db.query(Usuario).filter(
            Usuario.del_flag == False, 
            Usuario.rol_id == 3
        ).count()
        total_donantes = db.query(Usuario).filter(
            Usuario.del_flag == False, 
            Usuario.rol_id == 2
        ).count()
        
        # Contar usuarios por estatus
        usuarios_activos = db.query(Usuario).filter(
            Usuario.del_flag == False, 
            Usuario.estatus_id == 1
        ).count()
        usuarios_pendientes = db.query(Usuario).filter(
            Usuario.del_flag == False, 
            Usuario.estatus_id == 3
        ).count()
        
        return {
            "total_usuarios": total_usuarios,
            "total_beneficiarios": total_beneficiarios,
            "total_donantes": total_donantes,
            "usuarios_activos": usuarios_activos,
            "usuarios_pendientes": usuarios_pendientes
        }
        
    except Exception as e:
        logging.error(f"Error al obtener estadísticas: {str(e)}")
        raise HTTPException(status_code=500, detail=f"Error interno: {str(e)}")