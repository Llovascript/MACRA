from fastapi import APIRouter, HTTPException, Depends, status
from sqlalchemy.orm import Session
from sqlalchemy.exc import IntegrityError
from typing import List
from database import get_db
from models import Usuario, Evento, EstatusG, BeneficiarioEvento, DonanteEvento
from schemas import EventoCreate, EventoResponse, DonanteEventoResponse, BeneficiarioEventoResponse
from dependencies import require_role, get_current_active_user
import logging

logger = logging.getLogger(__name__)
router = APIRouter(prefix="/eventos", tags=["eventos"])


@router.post("/", response_model=EventoResponse)
def create_evento(
    evento: EventoCreate,
    db: Session = Depends(get_db),
    current_user: Usuario = Depends(require_role(["admin"]))
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
    current_user: Usuario = Depends(require_role(["admin"]))
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


@router.delete("/{evento_id}", status_code=status.HTTP_204_NO_CONTENT)
def delete_evento(
    evento_id: int,
    db: Session = Depends(get_db),
    current_user: Usuario = Depends(require_role(["admin"]))
):
    evento = db.query(Evento).filter(
        Evento.id == evento_id,
        Evento.del_flag == False
    ).first()

    if not evento:
        raise HTTPException(status_code=404, detail="Evento no encontrado")

    evento.del_flag = True
    try:
        db.commit()
        return {"message": "Evento eliminado exitosamente"}
    except Exception as e:
        db.rollback()
        raise HTTPException(status_code=400, detail="Error eliminando evento")


@router.post("/{evento_id}/beneficiarios/{beneficiario_id}")
def add_beneficiario(
    evento_id: int,
    beneficiario_id: int,
    db: Session = Depends(get_db),
    current_user: Usuario = Depends(require_role(["admin"]))
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
    current_user: Usuario = Depends(require_role(["admin"]))
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


@router.post("/{evento_id}/unirse_como_beneficiario", response_model=BeneficiarioEventoResponse)
def join_event_as_beneficiario(
    evento_id: int,
    db: Session = Depends(get_db),
    current_user: Usuario = Depends(require_role(["beneficiario"]))
):
    logger.info(f"Usuario {current_user.id} intentando unirse al evento {evento_id} como beneficiario")
    
    # Verificar que el evento existe y está activo
    evento = db.query(Evento).filter(
        Evento.id == evento_id, 
        Evento.del_flag == False
    ).first()
    if not evento:
        raise HTTPException(status_code=404, detail="Evento no encontrado")

    # Verificar que el usuario está aprobado
    if not current_user.aprobacion:
        raise HTTPException(status_code=403, detail="Usuario no aprobado")

    try:
        # Verificar si ya existe la relación (sin bloqueo)
        existing_entry = db.query(BeneficiarioEvento).filter(
            BeneficiarioEvento.evento_id == evento_id,
            BeneficiarioEvento.beneficiario_id == current_user.id
        ).first()
        
        if existing_entry:
            logger.info(f"Usuario {current_user.id} ya está unido al evento {evento_id}")
            raise HTTPException(status_code=409, detail="Ya estás unido a este evento como beneficiario")

        # Crear nueva entrada
        new_entry = BeneficiarioEvento(
            evento_id=evento_id, 
            beneficiario_id=current_user.id
        )
        db.add(new_entry)
        db.commit()
        db.refresh(new_entry)
        
        logger.info(f"Usuario {current_user.id} se unió exitosamente al evento {evento_id}")
        return new_entry
        
    except IntegrityError as e:
        db.rollback()
        logger.error(f"IntegrityError al unir usuario {current_user.id} al evento {evento_id}: {str(e)}")
        # Verificar si el error es por constraint de unicidad
        if "UNIQUE constraint failed" in str(e) or "duplicate key" in str(e).lower():
            raise HTTPException(status_code=409, detail="Ya estás unido a este evento como beneficiario")
        else:
            raise HTTPException(status_code=400, detail=f"Error de integridad: {str(e)}")
    except HTTPException:
        db.rollback()
        raise
    except Exception as e:
        db.rollback()
        logger.error(f"Error inesperado al unir usuario {current_user.id} al evento {evento_id}: {str(e)}")
        raise HTTPException(status_code=400, detail=f"Error al unirse al evento: {str(e)}")


@router.post("/{evento_id}/unirse_como_donante", response_model=DonanteEventoResponse)
def join_event_as_donante(
    evento_id: int,
    db: Session = Depends(get_db),
    current_user: Usuario = Depends(require_role(["donante"]))
):
    logger.info(f"Usuario {current_user.id} intentando unirse al evento {evento_id} como donante")
    
    # Verificar que el evento existe y está activo
    evento = db.query(Evento).filter(
        Evento.id == evento_id, 
        Evento.del_flag == False
    ).first()
    if not evento:
        raise HTTPException(status_code=404, detail="Evento no encontrado")

    # Verificar que el usuario está aprobado
    if not current_user.aprobacion:
        raise HTTPException(status_code=403, detail="Usuario no aprobado")

    try:
        # Verificar si ya existe la relación
        existing_entry = db.query(DonanteEvento).filter(
            DonanteEvento.evento_id == evento_id,
            DonanteEvento.donante_id == current_user.id
        ).first()
        
        if existing_entry:
            logger.info(f"Usuario {current_user.id} ya está unido al evento {evento_id}")
            raise HTTPException(status_code=409, detail="Ya estás unido a este evento como donante")

        # Crear nueva entrada
        new_entry = DonanteEvento(
            evento_id=evento_id, 
            donante_id=current_user.id
        )
        db.add(new_entry)
        db.commit()
        db.refresh(new_entry)
        
        logger.info(f"Usuario {current_user.id} se unió exitosamente al evento {evento_id}")
        return new_entry
        
    except IntegrityError as e:
        db.rollback()
        logger.error(f"IntegrityError al unir usuario {current_user.id} al evento {evento_id}: {str(e)}")
        if "UNIQUE constraint failed" in str(e) or "duplicate key" in str(e).lower():
            raise HTTPException(status_code=409, detail="Ya estás unido a este evento como donante")
        else:
            raise HTTPException(status_code=400, detail=f"Error de integridad: {str(e)}")
    except HTTPException:
        db.rollback()
        raise
    except Exception as e:
        db.rollback()
        logger.error(f"Error inesperado al unir usuario {current_user.id} al evento {evento_id}: {str(e)}")
        raise HTTPException(status_code=400, detail=f"Error al unirse al evento: {str(e)}")


@router.delete("/{evento_id}/salir_como_beneficiario")
def leave_event_as_beneficiario(
    evento_id: int,
    db: Session = Depends(get_db),
    current_user: Usuario = Depends(require_role(["beneficiario"]))
):
    logger.info(f"Usuario {current_user.id} intentando salir del evento {evento_id} como beneficiario")
    
    relation = db.query(BeneficiarioEvento).filter(
        BeneficiarioEvento.evento_id == evento_id,
        BeneficiarioEvento.beneficiario_id == current_user.id
    ).first()

    if not relation:
        raise HTTPException(status_code=404, detail="No estás unido a este evento")

    try:
        db.delete(relation)
        db.commit()
        logger.info(f"Usuario {current_user.id} salió exitosamente del evento {evento_id}")
        return {"message": "Has salido del evento exitosamente"}
    except Exception as e:
        db.rollback()
        logger.error(f"Error al salir del evento {evento_id}: {str(e)}")
        raise HTTPException(status_code=400, detail="Error al salir del evento")


@router.delete("/{evento_id}/salir_como_donante")
def leave_event_as_donante(
    evento_id: int,
    db: Session = Depends(get_db),
    current_user: Usuario = Depends(require_role(["donante"]))
):
    logger.info(f"Usuario {current_user.id} intentando salir del evento {evento_id} como donante")
    
    relation = db.query(DonanteEvento).filter(
        DonanteEvento.evento_id == evento_id,
        DonanteEvento.donante_id == current_user.id
    ).first()

    if not relation:
        raise HTTPException(status_code=404, detail="No estás unido a este evento")

    try:
        db.delete(relation)
        db.commit()
        logger.info(f"Usuario {current_user.id} salió exitosamente del evento {evento_id}")
        return {"message": "Has salido del evento exitosamente"}
    except Exception as e:
        db.rollback()
        logger.error(f"Error al salir del evento {evento_id}: {str(e)}")
        raise HTTPException(status_code=400, detail="Error al salir del evento")


# ENDPOINTS PARA CONSULTAR PARTICIPACIÓN

@router.get("/me/como_donante", response_model=List[DonanteEventoResponse])
def get_my_donante_events(
    db: Session = Depends(get_db),
    current_user: Usuario = Depends(require_role(["donante"]))
):
    """Obtener eventos donde el usuario actual participa como donante"""
    return db.query(DonanteEvento).filter(
        DonanteEvento.donante_id == current_user.id
    ).all()


@router.get("/me/como_beneficiario", response_model=List[BeneficiarioEventoResponse])
def get_my_beneficiario_events(
    db: Session = Depends(get_db),
    current_user: Usuario = Depends(require_role(["beneficiario"]))
):
    """Obtener eventos donde el usuario actual participa como beneficiario"""
    return db.query(BeneficiarioEvento).filter(
        BeneficiarioEvento.beneficiario_id == current_user.id
    ).all()


@router.get("/{evento_id}/participacion")
def check_participation(
    evento_id: int,
    db: Session = Depends(get_db),
    current_user: Usuario = Depends(get_current_active_user)
):
    """Verificar si el usuario actual participa en un evento específico"""
    is_donante = False
    is_beneficiario = False
    
    if current_user.rol.nombre == "donante":
        is_donante = db.query(DonanteEvento).filter(
            DonanteEvento.evento_id == evento_id,
            DonanteEvento.donante_id == current_user.id
        ).first() is not None
    
    if current_user.rol.nombre == "beneficiario":
        is_beneficiario = db.query(BeneficiarioEvento).filter(
            BeneficiarioEvento.evento_id == evento_id,
            BeneficiarioEvento.beneficiario_id == current_user.id
        ).first() is not None
    
    return {
        "evento_id": evento_id,
        "user_id": current_user.id,
        "is_donante": is_donante,
        "is_beneficiario": is_beneficiario
    }


@router.get("/capacidad", tags=["eventos"])
def get_capacidad_eventos(
    db: Session = Depends(get_db),
    current_user: Usuario = Depends(require_role(["admin"]))
):
    """
    Devuelve una lista de eventos con la cantidad de beneficiarios y donantes registrados en cada uno.
    Solo accesible para usuarios con rol 'admin'.
    """
    eventos = db.query(Evento).filter(Evento.del_flag == False).all()
    resultado = []

    for evento in eventos:
        beneficiarios_count = db.query(BeneficiarioEvento).filter(
            BeneficiarioEvento.evento_id == evento.id
        ).count()

        donantes_count = db.query(DonanteEvento).filter(
            DonanteEvento.evento_id == evento.id
        ).count()

        resultado.append({
            "evento_id": evento.id,
            "nombre_evento": evento.nombre,
            "beneficiarios": beneficiarios_count,
            "donantes": donantes_count
        })

    return resultado


@router.get("/{evento_id}/beneficiarios", response_model=List[BeneficiarioEventoResponse])
def get_evento_beneficiarios(
    evento_id: int,
    db: Session = Depends(get_db),
    current_user: Usuario = Depends(require_role(["admin", "moderador"]))
):
    """Obtener todos los beneficiarios de un evento específico"""
    evento = db.query(Evento).filter(
        Evento.id == evento_id,
        Evento.del_flag == False
    ).first()
    
    if not evento:
        raise HTTPException(status_code=404, detail="Evento no encontrado")
    
    return db.query(BeneficiarioEvento).filter(
        BeneficiarioEvento.evento_id == evento_id
    ).all()


@router.get("/{evento_id}/donantes", response_model=List[DonanteEventoResponse])
def get_evento_donantes(
    evento_id: int,
    db: Session = Depends(get_db),
    current_user: Usuario = Depends(require_role(["admin", "moderador"]))
):
    """Obtener todos los donantes de un evento específico"""
    evento = db.query(Evento).filter(
        Evento.id == evento_id,
        Evento.del_flag == False
    ).first()
    
    if not evento:
        raise HTTPException(status_code=404, detail="Evento no encontrado")
    
    return db.query(DonanteEvento).filter(
        DonanteEvento.evento_id == evento_id
    ).all()