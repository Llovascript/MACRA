from fastapi import APIRouter, HTTPException, Depends
from sqlalchemy.orm import Session
from database import get_db
from models import Usuario, Articulo, CategoriaArt, ArtPresentacion, Unidad
from schemas import ArticuloCreate, ArticuloResponse, ArtPresentacionCreate, ArtPresentacionResponse
from dependencies import require_role

router = APIRouter(tags=["articulos"])

@router.post("/articulos/", response_model=ArticuloResponse)
def create_articulo(
    articulo: ArticuloCreate, 
    db: Session = Depends(get_db),
    current_user: Usuario = Depends(require_role(["admin"]))
):
    categoria = db.query(CategoriaArt).filter(CategoriaArt.id == articulo.categoria_id).first()
    if not categoria:
        raise HTTPException(status_code=404, detail="Categoría no encontrada")
    
    db_articulo = Articulo(**articulo.dict())
    db.add(db_articulo)
    try:
        db.commit()
        db.refresh(db_articulo)
        return db_articulo
    except Exception as e:
        db.rollback()
        raise HTTPException(status_code=400, detail="Error creando artículo")



@router.post("/articulos/presentaciones/", response_model=ArtPresentacionResponse)
def create_presentacion(
    presentacion: ArtPresentacionCreate, 
    db: Session = Depends(get_db),
    current_user: Usuario = Depends(require_role(["admin"]))
):
    articulo = db.query(Articulo).filter(Articulo.id == presentacion.articulo_id).first()
    if not articulo:
        raise HTTPException(status_code=404, detail="Artículo no encontrado")
    
    unidad = db.query(Unidad).filter(Unidad.id == presentacion.unidad_id).first()
    if not unidad:
        raise HTTPException(status_code=404, detail="Unidad no encontrada")
    
    db_presentacion = ArtPresentacion(**presentacion.dict())
    db.add(db_presentacion)
    try:
        db.commit()
        db.refresh(db_presentacion)
        return db_presentacion
    except Exception as e:
        db.rollback()
        raise HTTPException(status_code=400, detail="Error creando presentación")
