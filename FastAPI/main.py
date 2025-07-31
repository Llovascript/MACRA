from fastapi import FastAPI
from fastapi.middleware.cors import CORSMiddleware
from database import SessionLocal, Base, engine
from models import Rol, EstatusG, CategoriaArt, Unidad

from endpoints.autenticacion_endpoints import router as autenticacion_router
from endpoints.roles_endpoints import router as roles_router
from endpoints.usuarios_endpoints import router as usuarios_router
from endpoints.ubicaciones_endpoints import router as ubicaciones_router
from endpoints.donaciones_endpoints import router as donaciones_router
from endpoints.eventos_endpoints import router as eventos_router
from endpoints.articulos_endpoints import router as articulos_router
from endpoints.paquetes_endpoints import router as paquetes_router
from endpoints.entregas_endpoints import router as entregas_router

from fastapi.middleware.cors import CORSMiddleware

Base.metadata.create_all(bind=engine)
app = FastAPI(
    title="Sistema de Donaciones API",
    description="API para gestionar donaciones, usuarios, roles y eventos",          
    version="1.0.0")

@app.get("/")
def read_root():
    return {
        "message": "Bienvenido a la API de Donaciones"
    }

app.add_middleware(
    CORSMiddleware,
    allow_origins=[
        "http://localhost:8000",    # Laravel
        "http://127.0.0.1:8000",   # Laravel alternativo
        "http://localhost:5001",    # FastAPI
        "http://127.0.0.1:5001"    # FastAPI alternativo
    ],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

app.include_router(autenticacion_router)
app.include_router(roles_router)
app.include_router(usuarios_router)
app.include_router(ubicaciones_router)
app.include_router(donaciones_router)
app.include_router(eventos_router)
app.include_router(articulos_router)
app.include_router(paquetes_router)
app.include_router(entregas_router)

@app.on_event("startup")
async def startup_event():
    db = SessionLocal()
    try:
        if db.query(Rol).count() == 0:
            default_roles = [
                Rol(nombre="admin"),
                Rol(nombre="donante"),
                Rol(nombre="beneficiario")
            ]
            db.add_all(default_roles)
        
        if db.query(EstatusG).count() == 0:
            default_estatus = [
                EstatusG(nombre="activo"),
                EstatusG(nombre="inactivo"),
                EstatusG(nombre="pendiente"),
                EstatusG(nombre="aprobado"),
                EstatusG(nombre="rechazado")
            ]
            db.add_all(default_estatus)
        
        if db.query(CategoriaArt).count() == 0:
            default_categories = [
                CategoriaArt(nombre="Perecederos"),
                CategoriaArt(nombre="No perecederos")
            ]
            db.add_all(default_categories)
        
        if db.query(Unidad).count() == 0:
            default_units = [
                Unidad(nombre="Kilogramo"),
                Unidad(nombre="Gramo"),
                Unidad(nombre="Litro"),
                Unidad(nombre="Mililitro"),
                Unidad(nombre="Pieza"),
                Unidad(nombre="Paquete"),
                Unidad(nombre="Caja")
            ]
            db.add_all(default_units)
        
        db.commit()
    finally:
        db.close()

if __name__ == "__main__":
    import uvicorn
    uvicorn.run(app, host="0.0.0.0", port=5001)
