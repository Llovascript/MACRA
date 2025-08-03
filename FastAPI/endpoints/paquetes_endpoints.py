from fastapi import APIRouter, HTTPException, Depends
from sqlalchemy.orm import Session
from database import get_db
from models import Usuario, Paquete
from schemas import PaqueteCreate, PaqueteResponse
from dependencies import require_role

router = APIRouter(prefix="/paquetes", tags=["paquetes"])

@router.post("/", response_model=PaqueteResponse)
def create_paquete(
    paquete: PaqueteCreate, 
    db: Session = Depends(get_db),
    current_user: Usuario = Depends(require_role(["admin"]))
):
    db_paquete = Paquete(**paquete.dict())
    db.add(db_paquete)
    try:
        db.commit()
        db.refresh(db_paquete)
        return db_paquete
    except Exception as e:
        db.rollback()
        raise HTTPException(status_code=400, detail="Error creando paquete")
