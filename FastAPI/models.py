from sqlalchemy import Column, Integer, String, Enum, Date, ForeignKey, Boolean, Text
from sqlalchemy.orm import relationship
from database import Base
import enum

# Tablas existentes (NO MODIFICAR)

class TipoUsuarioEnum(str, enum.Enum):
    persona = "persona"
    organizacion = "organizacion"

class TipoDonanteEnum(str, enum.Enum):
    persona = "persona"
    organizacion = "organizacion"

class Rol(Base):
    __tablename__ = 'roles'
    
    id = Column(Integer, primary_key=True, index=True)
    nombre = Column(String(50), unique=True)
    usuarios = relationship('Usuario', back_populates='rol')

class Usuario(Base):
    __tablename__ = 'usuarios'
    
    id = Column(Integer, primary_key=True, index=True)
    tipo = Column(Enum(TipoUsuarioEnum))
    nombre = Column(String(100))
    aP = Column(String(50), nullable=True)
    aM = Column(String(50), nullable=True)
    edad = Column(Integer, nullable=True)
    telefono = Column(String(20))
    correo = Column(String(100), unique=True, index=True)
    contraseña = Column(String(100))
    rfc = Column(String(13), nullable=True)
    paginaWeb = Column(String(200), nullable=True)
    fundacion = Column(Date, nullable=True)
    aprobacion = Column(Boolean, default=False)
    del_flag = Column('del', Boolean, default=False)
    
    rol_id = Column(Integer, ForeignKey('roles.id'))
    estatus_id = Column(Integer, ForeignKey('estatusG.id'), nullable=True)
    direccion_id = Column(Integer, ForeignKey('direcciones.id'), nullable=True)

    rol = relationship('Rol', back_populates='usuarios')
    estatus = relationship('EstatusG', back_populates='usuarios')
    direccion = relationship('Direccion', back_populates='usuarios')
    donaciones = relationship('Donacion', back_populates='usuario')
    beneficios_eventos = relationship('BeneficiarioEvento', back_populates='beneficiario')
    donaciones_eventos = relationship('DonanteEvento', back_populates='donante')

class Estado(Base):
    __tablename__ = 'estados'
    
    id = Column(Integer, primary_key=True, index=True)
    nombre = Column(String(150), nullable=False)
    municipios = relationship('Municipio', back_populates='estado')

class Municipio(Base):
    __tablename__ = 'municipios'
    
    id = Column(Integer, primary_key=True, index=True)
    nombre = Column(String(150), nullable=False)
    estado_id = Column(Integer, ForeignKey('estados.id'))
    
    estado = relationship('Estado', back_populates='municipios')
    colonias = relationship('Colonia', back_populates='municipio')

class Colonia(Base):
    __tablename__ = 'colonias'
    
    id = Column(Integer, primary_key=True, index=True)
    nombre = Column(String(150), nullable=False)
    cp = Column(String(20), nullable=False)
    municipio_id = Column(Integer, ForeignKey('municipios.id'))
    
    municipio = relationship('Municipio', back_populates='colonias')
    calles = relationship('Calle', back_populates='colonia')

class Calle(Base):
    __tablename__ = 'calles'
    
    id = Column(Integer, primary_key=True, index=True)
    nombre = Column(String(150), nullable=False)
    colonia_id = Column(Integer, ForeignKey('colonias.id'))
    
    colonia = relationship('Colonia', back_populates='calles')
    direcciones = relationship('Direccion', back_populates='calle')

class Direccion(Base):
    __tablename__ = 'direcciones'
    
    id = Column(Integer, primary_key=True, index=True)
    numExt = Column(Integer, nullable=False)
    numInt = Column(Integer, nullable=True)
    calle_id = Column(Integer, ForeignKey('calles.id'))
    
    calle = relationship('Calle', back_populates='direcciones')
    usuarios = relationship('Usuario', back_populates='direccion')

class EstatusG(Base):
    __tablename__ = 'estatusG'
    
    id = Column(Integer, primary_key=True, index=True)
    nombre = Column(String(150), nullable=False)
    
    usuarios = relationship('Usuario', back_populates='estatus')
    eventos = relationship('Evento', back_populates='estatus')
    donaciones = relationship('Donacion', back_populates='estatus')
    entregas = relationship('Entrega', back_populates='estatus')

class Unidad(Base):
    __tablename__ = 'unidades'
    
    id = Column(Integer, primary_key=True, index=True)
    nombre = Column(String(150), nullable=False)
    
    presentaciones = relationship('ArtPresentacion', back_populates='unidad')

class CategoriaArt(Base):
    __tablename__ = 'categoriasArt'
    
    id = Column(Integer, primary_key=True, index=True)
    nombre = Column(String(150), nullable=False)
    
    articulos = relationship('Articulo', back_populates='categoria')

class Articulo(Base):
    __tablename__ = 'articulos'
    
    id = Column(Integer, primary_key=True, index=True)
    nombre = Column(String(150), nullable=False)
    del_flag = Column('del', Boolean, default=False)
    categoria_id = Column(Integer, ForeignKey('categoriasArt.id'))
    
    categoria = relationship('CategoriaArt', back_populates='articulos')
    presentaciones = relationship('ArtPresentacion', back_populates='articulo')

class ArtPresentacion(Base):
    __tablename__ = 'artPresentacion'
    
    id = Column(Integer, primary_key=True, index=True)
    cantidad = Column(Integer, nullable=False)
    del_flag = Column('del', Boolean, default=False)
    articulo_id = Column(Integer, ForeignKey('articulos.id'))
    unidad_id = Column(Integer, ForeignKey('unidades.id'))
    
    articulo = relationship('Articulo', back_populates='presentaciones')
    unidad = relationship('Unidad', back_populates='presentaciones')
    donaciones = relationship('Donacion', back_populates='articuloP')
    stock = relationship('StockArt', back_populates='articuloP')
    contenido_paquetes = relationship('ContPaq', back_populates='articuloP')

class StockArt(Base):
    __tablename__ = 'stockArt'
    
    id = Column(Integer, primary_key=True, index=True)
    cantidad = Column(Integer, nullable=False)
    del_flag = Column('del', Boolean, default=False)
    articuloP_id = Column(Integer, ForeignKey('artPresentacion.id'))
    
    articuloP = relationship('ArtPresentacion', back_populates='stock')

class Paquete(Base):
    __tablename__ = 'paquetes'
    
    id = Column(Integer, primary_key=True, index=True)
    nombre = Column(String(150), nullable=False)
    del_flag = Column('del', Boolean, default=False)
    
    contenido = relationship('ContPaq', back_populates='paquete')
    entregas = relationship('Entrega', back_populates='paquete')

class ContPaq(Base):
    __tablename__ = 'contPaq'
    
    id = Column(Integer, primary_key=True, index=True)
    cantidad = Column(Integer, nullable=False)
    del_flag = Column('del', Boolean, default=False)
    paquete_id = Column(Integer, ForeignKey('paquetes.id'))
    articuloP_id = Column(Integer, ForeignKey('artPresentacion.id'))
    
    paquete = relationship('Paquete', back_populates='contenido')
    articuloP = relationship('ArtPresentacion', back_populates='contenido_paquetes')

class Evento(Base):
    __tablename__ = 'eventos'
    
    id = Column(Integer, primary_key=True, index=True)
    nombre = Column(String(150), nullable=False)
    fechaIn = Column(Date, nullable=False)
    fechaTer = Column(Date, nullable=False)
    descripcion = Column(String(250), nullable=False)
    del_flag = Column('del', Boolean, default=False)
    estatus_id = Column(Integer, ForeignKey('estatusG.id'))
    
    estatus = relationship('EstatusG', back_populates='eventos')
    entregas = relationship('Entrega', back_populates='evento')
    beneficiarios = relationship('BeneficiarioEvento', back_populates='evento')
    donantes = relationship('DonanteEvento', back_populates='evento')

class Entrega(Base):
    __tablename__ = 'entregas'
    
    id = Column(Integer, primary_key=True, index=True)
    cantidad = Column(Integer, nullable=False)
    fecha = Column(Date, nullable=False)
    del_flag = Column('del', Boolean, default=False)
    paquete_id = Column(Integer, ForeignKey('paquetes.id'))
    evento_id = Column(Integer, ForeignKey('eventos.id'))
    estatus_id = Column(Integer, ForeignKey('estatusG.id'))
    
    paquete = relationship('Paquete', back_populates='entregas')
    evento = relationship('Evento', back_populates='entregas')
    estatus = relationship('EstatusG', back_populates='entregas')

class Donacion(Base):
    __tablename__ = 'donaciones'
    
    id = Column(Integer, primary_key=True, index=True)
    tipo_donante = Column(Enum(TipoDonanteEnum))
    fecha = Column(Date, nullable=False)
    cantidad = Column(Integer, nullable=False)
    aprobacion = Column(Boolean, default=False)
    del_flag = Column('del', Boolean, default=False)
    usuario_id = Column(Integer, ForeignKey('usuarios.id'))
    articuloP_id = Column(Integer, ForeignKey('artPresentacion.id'))
    estatus_id = Column(Integer, ForeignKey('estatusG.id'))
    
    usuario = relationship('Usuario', back_populates='donaciones')
    articuloP = relationship('ArtPresentacion', back_populates='donaciones')
    estatus = relationship('EstatusG', back_populates='donaciones')

class BeneficiarioEvento(Base):
    __tablename__ = 'beneficiariosEventos'
    
    id = Column(Integer, primary_key=True, index=True)
    del_flag = Column('del', Boolean, default=False)
    evento_id = Column(Integer, ForeignKey('eventos.id'))
    beneficiario_id = Column(Integer, ForeignKey('usuarios.id'))
    
    evento = relationship('Evento', back_populates='beneficiarios')
    beneficiario = relationship('Usuario', back_populates='beneficios_eventos')

class DonanteEvento(Base):
    __tablename__ = 'donantesEventos'
    
    id = Column(Integer, primary_key=True, index=True)
    del_flag = Column('del', Boolean, default=False)
    evento_id = Column(Integer, ForeignKey('eventos.id'))
    donante_id = Column(Integer, ForeignKey('usuarios.id'))
    
    evento = relationship('Evento', back_populates='donantes')
    donante = relationship('Usuario', back_populates='donaciones_eventos')