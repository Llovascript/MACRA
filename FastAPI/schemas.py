from pydantic import BaseModel, EmailStr
from typing import Optional, List
from datetime import date
from models import TipoUsuarioEnum, TipoDonanteEnum


class Token(BaseModel):
    access_token: str
    token_type: str

class TokenData(BaseModel):
    username: Optional[str] = None
    user_id: Optional[int] = None

class LoginRequest(BaseModel):
    correo: EmailStr
    contraseña: str

class RolBase(BaseModel):
    nombre: str

class RolCreate(RolBase):
    pass

class RolResponse(RolBase):
    id: int
    
    class Config:
        from_attributes = True

class UsuarioBase(BaseModel):
    tipo: TipoUsuarioEnum
    nombre: str
    aP: Optional[str] = None
    aM: Optional[str] = None
    edad: Optional[int] = None
    telefono: str
    correo: EmailStr
    rfc: Optional[str] = None
    paginaWeb: Optional[str] = None
    fundacion: Optional[date] = None
    rol_id: int
    estatus_id: Optional[int] = None
    direccion_id: Optional[int] = None
    aprobacion: Optional[bool] = False

class UsuarioCreate(UsuarioBase):
    contraseña: str

class UsuarioUpdate(BaseModel):
    tipo: Optional[TipoUsuarioEnum] = None
    nombre: Optional[str] = None
    aP: Optional[str] = None
    aM: Optional[str] = None
    edad: Optional[int] = None
    telefono: Optional[str] = None
    correo: Optional[EmailStr] = None
    contraseña: Optional[str] = None
    rfc: Optional[str] = None
    paginaWeb: Optional[str] = None
    fundacion: Optional[date] = None
    rol_id: Optional[int] = None
    estatus_id: Optional[int] = None
    direccion_id: Optional[int] = None
    aprobacion: Optional[bool] = None

class BeneficiarioEventoResponse(BaseModel):
    id: int
    del_flag: bool
    evento_id: int
    beneficiario_id: int

    class Config:
        from_attributes = True

class DonanteEventoBase(BaseModel):
    evento_id: int
    donante_id: int

class DonanteEventoCreate(DonanteEventoBase):
    pass

class DonanteEventoResponse(DonanteEventoBase):
    id: int
    del_flag: bool

    class Config:
        from_attributes = True

class UsuarioResponse(UsuarioBase):
    id: int
    del_flag: bool
    rol: Optional[RolResponse] = None
    beneficios_eventos: List[BeneficiarioEventoResponse] = []
    donaciones_eventos: List[DonanteEventoResponse] = []
    
    class Config:
        from_attributes = True

class EstadoBase(BaseModel):
    nombre: str

class EstadoCreate(EstadoBase):
    pass

class EstadoResponse(EstadoBase):
    id: int
    
    class Config:
        from_attributes = True

class MunicipioBase(BaseModel):
    nombre: str
    estado_id: int

class MunicipioCreate(MunicipioBase):
    pass

class MunicipioResponse(MunicipioBase):
    id: int
    
    class Config:
        from_attributes = True

class EstatusGBase(BaseModel):
    nombre: str

class EstatusGCreate(EstatusGBase):
    pass

class EstatusGResponse(EstatusGBase):
    id: int
    
    class Config:
        from_attributes = True

class DonacionBase(BaseModel):
    tipo_donante: TipoDonanteEnum
    fecha: date
    cantidad: int
    usuario_id: int
    articuloP_id: int
    estatus_id: int
    aprobacion: Optional[bool] = False

class DonacionCreate(DonacionBase):
    pass

class DonacionUpdate(BaseModel):
    tipo_donante: Optional[TipoDonanteEnum] = None
    fecha: Optional[date] = None
    cantidad: Optional[int] = None
    usuario_id: Optional[int] = None
    articuloP_id: Optional[int] = None
    estatus_id: Optional[int] = None
    aprobacion: Optional[bool] = None

class DonacionResponse(DonacionBase):
    id: int
    del_flag: bool
    
    class Config:
        from_attributes = True

class EventoBase(BaseModel):
    nombre: str
    fechaIn: date
    fechaTer: date
    descripcion: str
    estatus_id: int

class EventoCreate(EventoBase):
    pass

class EventoResponse(EventoBase):
    id: int
    del_flag: bool
    beneficiarios: List[BeneficiarioEventoResponse] = []
    donantes: List[DonanteEventoResponse] = []
    
    class Config:
        from_attributes = True

class ArticuloBase(BaseModel):
    nombre: str
    categoria_id: int

class ArticuloCreate(ArticuloBase):
    pass

class ArticuloResponse(ArticuloBase):
    id: int
    del_flag: bool
    
    class Config:
        from_attributes = True

class ArtPresentacionBase(BaseModel):
    cantidad: int
    articulo_id: int
    unidad_id: int

class ArtPresentacionCreate(ArtPresentacionBase):
    pass

class ArtPresentacionResponse(ArtPresentacionBase):
    id: int
    del_flag: bool
    
    class Config:
        from_attributes = True

class PaqueteBase(BaseModel):
    nombre: str

class PaqueteCreate(PaqueteBase):
    pass

class PaqueteResponse(PaqueteBase):
    id: int
    del_flag: bool
    
    class Config:
        from_attributes = True

class EntregaBase(BaseModel):
    cantidad: int
    fecha: date
    paquete_id: int
    evento_id: int
    estatus_id: int

class EntregaCreate(EntregaBase):
    pass

class EntregaResponse(EntregaBase):
    id: int
    del_flag: bool
    
    class Config:
        from_attributes = True
