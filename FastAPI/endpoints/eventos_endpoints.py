from fastapi import APIRouter, HTTPException, Depends
from sqlalchemy.orm import Session
from typing import List
from database import get_db
from models import Usuario, Evento, EstatusG, BeneficiarioEvento
from schemas import EventoCreate, EventoResponse
from dependencies import require_role, get_current_active_user

router = APIRouter(prefix="/eventos", tags=["eventos"])

@router.post("/", response_model=EventoResponse)
def create_evento(
    evento: EventoCreate, 
    db: Session = Depends(get_db),
    current_user: Usuario = Depends(require_role(["admin", "moderador"]))
):
    estatus = db.query(EstatusG).filter(EstatusG.id == evento.estatus_id).first()
    if not estatus:
        raise HTTPException(status_code=404, detail="Estatus no válido")
    
    db_evento = Evento(**evento.dict())
    db.add(db_evento)
    try:
        db.commit()
        db.refresh(db_evento)
        return db_evento
    except Exception as e:
        db.rollback()
        raise HTTPException(status_code=400, detail="Error creando evento")

@router.get("/", response_model=List[EventoResponse])
def get_eventos(
    skip: int = 0, 
    limit: int = 100, 
    db: Session = Depends(get_db),
    current_user: Usuario = Depends(get_current_active_user)
):
    return db.query(Evento).filter(Evento.del_flag == False).offset(skip).limit(limit).all()

@router.put("/{evento_id}", response_model=EventoResponse)
def update_evento(
    evento_id: int, 
    evento_update: EventoCreate, 
    db: Session = Depends(get_db),
    current_user: Usuario = Depends(require_role(["admin", "moderador"]))
):
    evento = db.query(Evento).filter(
        Evento.id == evento_id,
        Evento.del_flag == False
    ).first()
    
    if not evento:
        raise HTTPException(status_code=404, detail="Evento no encontrado")
    
    update_data = evento_update.dict(exclude_unset=True)
    for field, value in update_data.items():
        setattr(evento, field, value)
    
    try:
        db.commit()
        db.refresh(evento)
        return evento
    except Exception as e:
        db.rollback()
        raise HTTPException(status_code=400, detail="Error actualizando evento")

@router.delete("/{evento_id}")
def delete_evento(
    evento_id: int,
    db: Session = Depends(get_db),
    current_user: Usuario = Depends(require_role(["admin", "moderador"]))
):
    evento = db.query(Evento).filter(
        Evento.id == evento_id,
        Evento.del_flag == False
    ).first()

    if not evento:
        raise HTTPException(status_code=404, detail="Evento no encontrado")

    try:
        evento.del_flag = True
        db.commit()
        return {"message": "Evento eliminado correctamente"}
    except Exception as e:
        db.rollback()
        raise HTTPException(status_code=400, detail="Error al eliminar el evento")

@router.post("/{evento_id}/beneficiarios/{beneficiario_id}")
def add_beneficiario(
    evento_id: int,
    beneficiario_id: int,
    db: Session = Depends(get_db),
    current_user: Usuario = Depends(require_role(["admin", "moderador"]))
):
    evento = db.query(Evento).filter(
        Evento.id == evento_id,
        Evento.del_flag == False
    ).first()
    if not evento:
        raise HTTPException(status_code=404, detail="Evento no encontrado")

    beneficiario = db.query(Usuario).filter(
        Usuario.id == beneficiario_id,
        Usuario.del_flag == False,
        Usuario.aprobacion == True
    ).first()
    if not beneficiario:
        raise HTTPException(status_code=404, detail="Beneficiario no válido")
    
    existing = db.query(BeneficiarioEvento).filter(
        BeneficiarioEvento.evento_id == evento_id,
        BeneficiarioEvento.beneficiario_id == beneficiario_id
    ).first()
    
    if existing:
        raise HTTPException(status_code=400, detail="El beneficiario ya está asignado a este evento")
    
    db_relation = BeneficiarioEvento(
        evento_id=evento_id,
        beneficiario_id=beneficiario_id
    )
    
    db.add(db_relation)
    try:
        db.commit()
        return {"message": "Beneficiario añadido al evento exitosamente"}
    except Exception as e:
        db.rollback()
        raise HTTPException(status_code=400, detail="Error añadiendo beneficiario")

@router.delete("/{evento_id}/beneficiarios/{beneficiario_id}")
def remove_beneficiario(
    evento_id: int,
    beneficiario_id: int,
    db: Session = Depends(get_db),
    current_user: Usuario = Depends(require_role(["admin", "moderador"]))
):
    relation = db.query(BeneficiarioEvento).filter(
        BeneficiarioEvento.evento_id == evento_id,
        BeneficiarioEvento.beneficiario_id == beneficiario_id
    ).first()
    
    if not relation:
        raise HTTPException(status_code=404, detail="Relación no encontrada")
    
    db.delete(relation)
    try:
        db.commit()
        return {"message": "Beneficiario removido del evento exitosamente"}
    except Exception as e:
        db.rollback()
        raise HTTPException(status_code=400, detail="Error removiendo beneficiario")
