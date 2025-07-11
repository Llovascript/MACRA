from fastapi import APIRouter, HTTPException, Depends
from sqlalchemy.orm import Session
from typing import List
from database import get_db
from models import Usuario, Rol
from schemas import RolCreate, RolResponse
from dependencies import require_role, get_current_active_user

router = APIRouter(prefix="/roles", tags=["roles"])

@router.post("/", response_model=RolResponse)
def create_rol(
    rol: RolCreate, 
    db: Session = Depends(get_db),
    current_user: Usuario = Depends(require_role(["admin"]))
):
    db_rol = Rol(**rol.dict())
    db.add(db_rol)
    try:
        db.commit()
        db.refresh(db_rol)
        return db_rol
    except Exception as e:
        db.rollback()
        raise HTTPException(status_code=400, detail="Rol already exists or invalid data")

@router.get("/", response_model=List[RolResponse])
def get_roles(
    db: Session = Depends(get_db),
    current_user: Usuario = Depends(get_current_active_user)
):
    return db.query(Rol).all()

@router.get("/{rol_id}", response_model=RolResponse)
def get_rol(
    rol_id: int, 
    db: Session = Depends(get_db),
    current_user: Usuario = Depends(get_current_active_user)
):
    rol = db.query(Rol).filter(Rol.id == rol_id).first()
    if not rol:
        raise HTTPException(status_code=404, detail="Rol not found")
    return rol
