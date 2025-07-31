from fastapi import APIRouter, HTTPException, Depends
from sqlalchemy.orm import Session
from database import get_db
from models import Usuario, Entrega, Paquete, Evento, EstatusG
from schemas import EntregaCreate, EntregaResponse
from dependencies import require_role

router = APIRouter(prefix="/entregas", tags=["entregas"])

@router.post("/", response_model=EntregaResponse)
def create_entrega(
    entrega: EntregaCreate, 
    db: Session = Depends(get_db),
    current_user: Usuario = Depends(require_role(["admin"]))
):
    paquete = db.query(Paquete).filter(Paquete.id == entrega.paquete_id).first()
    if not paquete:
        raise HTTPException(status_code=404, detail="Paquete no encontrado")
    
    evento = db.query(Evento).filter(Evento.id == entrega.evento_id).first()
    if not evento:
        raise HTTPException(status_code=404, detail="Evento no encontrado")
    
    estatus = db.query(EstatusG).filter(EstatusG.id == entrega.estatus_id).first()
    if not estatus:
        raise HTTPException(status_code=404, detail="Estatus no válido")
    
    db_entrega = Entrega(**entrega.dict())
    db.add(db_entrega)
    try:
        db.commit()
        db.refresh(db_entrega)
        return db_entrega
    except Exception as e:
        db.rollback()
        raise HTTPException(status_code=400, detail="Error creando entrega")
