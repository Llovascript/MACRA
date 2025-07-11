from fastapi import APIRouter, HTTPException, Depends
from sqlalchemy.orm import Session
from typing import List
from database import get_db
from models import Usuario, Estado, Municipio
from schemas import EstadoCreate, EstadoResponse, MunicipioCreate, MunicipioResponse
from dependencies import require_role, get_current_active_user

router = APIRouter(tags=["ubicaciones"])

@router.post("/estados/", response_model=EstadoResponse)
def create_estado(
    estado: EstadoCreate, 
    db: Session = Depends(get_db),
    current_user: Usuario = Depends(require_role(["admin", "moderador"]))
):
    db_estado = Estado(**estado.dict())
    db.add(db_estado)
    try:
        db.commit()
        db.refresh(db_estado)
        return db_estado
    except Exception as e:
        db.rollback()
        raise HTTPException(status_code=400, detail="Error creating estado")

@router.get("/estados/", response_model=List[EstadoResponse])
def get_estados(
    db: Session = Depends(get_db),
    current_user: Usuario = Depends(get_current_active_user)
):
    return db.query(Estado).all()

@router.post("/municipios/", response_model=MunicipioResponse)
def create_municipio(
    municipio: MunicipioCreate, 
    db: Session = Depends(get_db),
    current_user: Usuario = Depends(require_role(["admin", "moderador"]))
):
    estado = db.query(Estado).filter(Estado.id == municipio.estado_id).first()
    if not estado:
        raise HTTPException(status_code=400, detail="Estado no encontrado")
    
    db_municipio = Municipio(**municipio.dict())
    db.add(db_municipio)
    try:
        db.commit()
        db.refresh(db_municipio)
        return db_municipio
    except Exception as e:
        db.rollback()
        raise HTTPException(status_code=400, detail="Error creating municipio")
