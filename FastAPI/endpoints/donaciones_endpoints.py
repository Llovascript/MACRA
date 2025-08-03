from fastapi import APIRouter, HTTPException, Depends, status
from sqlalchemy.orm import Session
from typing import List
from database import get_db
from models import Usuario, Rol, Donacion, ArtPresentacion, EstatusG
from schemas import DonacionCreate, DonacionUpdate, DonacionResponse
from dependencies import require_role, get_current_active_user

router = APIRouter(prefix="/donaciones", tags=["donaciones"])

@router.post("/", response_model=DonacionResponse)
def create_donacion(
    donacion: DonacionCreate, 
    db: Session = Depends(get_db),
    current_user: Usuario = Depends(require_role(["admin", "donante"]))
):
    
    if current_user.rol.nombre == "donante" and donacion.usuario_id != current_user.id:
        raise HTTPException(status_code=status.HTTP_403_FORBIDDEN, detail="No tienes permiso para crear donaciones para otros usuarios.")
    
    usuario = db.query(Usuario).filter(
        Usuario.id == donacion.usuario_id,
        Usuario.del_flag == False
    ).first()
    if not usuario:
        raise HTTPException(status_code=404, detail="Usuario no encontrado")

    articulo = db.query(ArtPresentacion).filter(
        ArtPresentacion.id == donacion.articuloP_id,
        ArtPresentacion.del_flag == False
    ).first()
    if not articulo:
        raise HTTPException(status_code=404, detail="Artículo no encontrado")
    
    estatus = db.query(EstatusG).filter(EstatusG.id == donacion.estatus_id).first()
    if not estatus:
        raise HTTPException(status_code=404, detail="Estatus no válido")
    
    db_donacion = Donacion(**donacion.dict())
    db.add(db_donacion)
    try:
        db.commit()
        db.refresh(db_donacion)
        return db_donacion
    except Exception as e:
        db.rollback()
        raise HTTPException(status_code=400, detail="Error creando donación")

@router.get("/", response_model=List[DonacionResponse])
def get_donaciones(
    skip: int = 0, 
    limit: int = 100, 
    db: Session = Depends(get_db),
    current_user: Usuario = Depends(require_role(["admin"]))
):
    return db.query(Donacion).filter(Donacion.del_flag == False).offset(skip).limit(limit).all()

@router.get("/me", response_model=List[DonacionResponse])
def get_my_donations(
    db: Session = Depends(get_db),
    current_user: Usuario = Depends(require_role(["donante"])) # Solo donantes ven sus propias donaciones es el nuevo endpoin de la nueva tabla
):
    return db.query(Donacion).filter(
        Donacion.usuario_id == current_user.id,
        Donacion.del_flag == False
    ).all()

@router.get("/{donacion_id}", response_model=DonacionResponse)
def get_donacion(
    donacion_id: int, 
    db: Session = Depends(get_db),
    current_user: Usuario = Depends(get_current_active_user)
):
    donacion = db.query(Donacion).filter(
        Donacion.id == donacion_id,
        Donacion.del_flag == False
    ).first()
    
    if not donacion:
        raise HTTPException(status_code=404, detail="Donación no encontrada")
    
    if current_user.rol.nombre == "admin":
        return donacion
    elif current_user.rol.nombre == "donante" and current_user.id == donacion.usuario_id:
        return donacion
    else:
        raise HTTPException(
            status_code=status.HTTP_403_FORBIDDEN,
            detail="No tienes permiso para ver esta donación"
        )

@router.put("/{donacion_id}", response_model=DonacionResponse)
def update_donacion(
    donacion_id: int, 
    donacion_update: DonacionUpdate, 
    db: Session = Depends(get_db),
    current_user: Usuario = Depends(require_role(["admin"]))
):
    donacion = db.query(Donacion).filter(
        Donacion.id == donacion_id,
        Donacion.del_flag == False
    ).first()
    
    if not donacion:
        raise HTTPException(status_code=404, detail="Donación no encontrada")
    
    update_data = donacion_update.dict(exclude_unset=True)
    for field, value in update_data.items():
        setattr(donacion, field, value)
    
    try:
        db.commit()
        db.refresh(donacion)
        return donacion
    except Exception as e:
        db.rollback()
        raise HTTPException(status_code=400, detail="Error actualizando donación")
    
@router.put("/{donacion_id}/aprobar", response_model=DonacionResponse)
def approve_donacion(
    donacion_id: int,
    aprobacion: bool,
    db: Session = Depends(get_db),
    current_user: Usuario = Depends(require_role(["admin"]))
):
    donacion = db.query(Donacion).filter(
        Donacion.id == donacion_id,
        Donacion.del_flag == False
    ).first()
    
    if not donacion:
        raise HTTPException(status_code=404, detail="Donación no encontrada")
    
    donacion.aprobacion = aprobacion
    try:
        db.commit()
        db.refresh(donacion)
        return donacion
    except Exception as e:
        db.rollback()
        raise HTTPException(status_code=400, detail="Error actualizando aprobación de donación")
