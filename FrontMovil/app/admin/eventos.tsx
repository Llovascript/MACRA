"use client"

import { useState, useEffect } from "react"
import {
  View,
  Text,
  StyleSheet,
  TouchableOpacity,
  SafeAreaView,
  FlatList,
  Alert,
  ActivityIndicator,
  RefreshControl,
  Modal,
  TextInput,
  ScrollView,
} from "react-native"
import { useRouter } from "expo-router"
import { MaterialCommunityIcons } from "@expo/vector-icons"
import AsyncStorage from "@react-native-async-storage/async-storage"
import { generateFastApiUrl } from "@/utils"

interface EstatusG {
  id: number
  nombre: string
}

interface Evento {
  id: number
  nombre: string
  fechaIn: string
  fechaTer: string
  descripcion: string
  del_flag: boolean
  estatus_id: number
  estatus?: EstatusG
}

interface EventoCreate {
  nombre: string
  fechaIn: string
  fechaTer: string
  descripcion: string
  estatus_id: number
}

export default function AdminEventos() {
  const [eventos, setEventos] = useState<Evento[]>([])
  const [estatuses, setEstatuses] = useState<EstatusG[]>([])
  const [loading, setLoading] = useState(true)
  const [refreshing, setRefreshing] = useState(false)
  const [currentPage, setCurrentPage] = useState(1)
  const [totalPages, setTotalPages] = useState(1)
  const [showCreateModal, setShowCreateModal] = useState(false)
  const [showEditModal, setShowEditModal] = useState(false)
  const [editingEvento, setEditingEvento] = useState<Evento | null>(null)
  const [formData, setFormData] = useState<EventoCreate>({
    nombre: "",
    fechaIn: "",
    fechaTer: "",
    descripcion: "",
    estatus_id: 1,
  })
  const router = useRouter()
  const itemsPerPage = 10

  useEffect(() => {
    fetchEventos()
    fetchEstatuses()
  }, [])

  const fetchEventos = async () => {
    try {
      const token = await AsyncStorage.getItem("token")
      if (!token) {
        Alert.alert("Sesión expirada", "Por favor inicia sesión nuevamente")
        router.replace("/login")
        return
      }

      const response = await fetch(generateFastApiUrl("/eventos/"), {
        headers: {
          Authorization: `Bearer ${token}`,
          "Content-Type": "application/json",
        },
      })

      if (response.ok) {
        const data = await response.json()
        // Filtrar eventos no eliminados
        const eventosActivos = data.filter((evento: Evento) => !evento.del_flag)
        setEventos(eventosActivos)
        setTotalPages(Math.ceil(eventosActivos.length / itemsPerPage))
      } else if (response.status === 401) {
        Alert.alert("Sesión expirada", "Por favor inicia sesión nuevamente")
        await AsyncStorage.removeItem("token")
        router.replace("/login")
      } else {
        Alert.alert("Error", "No se pudieron cargar los eventos")
      }
    } catch (error) {
      console.error("Error fetching eventos:", error)
      Alert.alert("Error", "Error de conexión")
    } finally {
      setLoading(false)
      setRefreshing(false)
    }
  }

  const fetchEstatuses = async () => {
    try {
      const token = await AsyncStorage.getItem("token")
      if (!token) return

      // Intentar obtener estatuses de la base de datos
      // const response = await fetch(generateFastApiUrl("/estatuses/"), {
      //   headers: {
      //     Authorization: `Bearer ${token}`,
      //   },
      // })

      // if (response.ok) {
      //   const data = await response.json()
      //   setEstatuses(data)
      // } else {
        // Si no existe el endpoint, usar valores por defecto basados en la base de datos
        setEstatuses([
          { id: 1, nombre: "Activo" },
          { id: 2, nombre: "Inactivo" },
        ])
      //}
    } catch (error) {
      console.error("Error fetching estatuses:", error)
      // Valores por defecto si hay error
      setEstatuses([
        { id: 1, nombre: "Activo" },
        { id: 2, nombre: "Inactivo" },
      ])
    }
  }

  const handleCreateEvento = async () => {
    if (!formData.nombre || !formData.fechaIn || !formData.fechaTer || !formData.descripcion) {
      Alert.alert("Error", "Por favor completa todos los campos")
      return
    }

    try {
      const token = await AsyncStorage.getItem("token")
      if (!token) {
        router.replace("/login")
        return
      }

      // Para crear, siempre usar estatus_id = 1 (Activo)
      const createData = {
        ...formData,
        estatus_id: 1,
      }

      const response = await fetch(generateFastApiUrl("/eventos/"), {
        method: "POST",
        headers: {
          Authorization: `Bearer ${token}`,
          "Content-Type": "application/json",
        },
        body: JSON.stringify(createData),
      })

      if (response.ok) {
        Alert.alert("Éxito", "Evento creado correctamente")
        setShowCreateModal(false)
        resetForm()
        fetchEventos()
      } else {
        const errorData = await response.json()
        Alert.alert("Error", errorData.detail || "No se pudo crear el evento")
      }
    } catch (error) {
      console.error("Error creating evento:", error)
      Alert.alert("Error", "Error de conexión")
    }
  }

  const handleUpdateEvento = async () => {
    if (!editingEvento || !formData.nombre || !formData.fechaIn || !formData.fechaTer || !formData.descripcion) {
      Alert.alert("Error", "Por favor completa todos los campos")
      return
    }

    try {
      const token = await AsyncStorage.getItem("token")
      if (!token) {
        router.replace("/login")
        return
      }

      const response = await fetch(generateFastApiUrl(`/eventos/${editingEvento.id}`), {
        method: "PUT",
        headers: {
          Authorization: `Bearer ${token}`,
          "Content-Type": "application/json",
        },
        body: JSON.stringify(formData),
      })

      if (response.ok) {
        Alert.alert("Éxito", "Evento actualizado correctamente")
        setShowEditModal(false)
        setEditingEvento(null)
        resetForm()
        fetchEventos()
      } else {
        const errorData = await response.json()
        Alert.alert("Error", errorData.detail || "No se pudo actualizar el evento")
      }
    } catch (error) {
      console.error("Error updating evento:", error)
      Alert.alert("Error", "Error de conexión")
    }
  }

  const handleDeleteEvento = async (eventoId: number, eventoNombre: string) => {
    Alert.alert("Confirmar Eliminación", `¿Estás seguro de que quieres eliminar el evento "${eventoNombre}"?`, [
      { text: "Cancelar", style: "cancel" },
      {
        text: "Eliminar",
        style: "destructive",
        onPress: async () => {
          try {
            const token = await AsyncStorage.getItem("token")
            if (!token) {
              router.replace("/login")
              return
            }

            const response = await fetch(generateFastApiUrl(`/eventos/${eventoId}`), {
              method: "DELETE",
              headers: {
                Authorization: `Bearer ${token}`,
              },
            })

            if (response.ok) {
              Alert.alert("Éxito", "Evento eliminado correctamente")
              fetchEventos()
            } else {
              Alert.alert("Error", "No se pudo eliminar el evento")
            }
          } catch (error) {
            console.error("Error deleting evento:", error)
            Alert.alert("Error", "Error de conexión")
          }
        },
      },
    ])
  }

  const openEditModal = (evento: Evento) => {
    setEditingEvento(evento)
    setFormData({
      nombre: evento.nombre,
      fechaIn: evento.fechaIn,
      fechaTer: evento.fechaTer,
      descripcion: evento.descripcion,
      estatus_id: evento.estatus_id,
    })
    setShowEditModal(true)
  }

  const resetForm = () => {
    setFormData({
      nombre: "",
      fechaIn: "",
      fechaTer: "",
      descripcion: "",
      estatus_id: 1,
    })
  }

  const onRefresh = () => {
    setRefreshing(true)
    setCurrentPage(1)
    fetchEventos()
  }

  const formatDate = (dateString: string) => {
    try {
      const date = new Date(dateString)
      return date.toLocaleDateString("es-ES", {
        day: "2-digit",
        month: "2-digit",
        year: "numeric",
      })
    } catch (error) {
      return dateString
    }
  }

  const getEstatusName = (estatusId: number) => {
    const estatus = estatuses.find((e) => e.id === estatusId)
    return estatus?.nombre || "Activo" // Por defecto "Activo" en lugar de "Desconocido"
  }

  const getCurrentPageItems = () => {
    const startIndex = (currentPage - 1) * itemsPerPage
    const endIndex = startIndex + itemsPerPage
    return eventos.slice(startIndex, endIndex)
  }

  const goToNextPage = () => {
    if (currentPage < totalPages) {
      setCurrentPage(currentPage + 1)
    }
  }

  const goToPreviousPage = () => {
    if (currentPage > 1) {
      setCurrentPage(currentPage - 1)
    }
  }

  const renderEvento = ({ item }: { item: Evento }) => (
    <View style={styles.tableRow}>
      <View style={[styles.cell, styles.nombreColumn]}>
        <Text style={styles.cellText} numberOfLines={2}>
          {item.nombre}
        </Text>
      </View>
      <View style={[styles.cell, styles.fechaColumn]}>
        <Text style={styles.cellText}>{formatDate(item.fechaIn)}</Text>
      </View>
      <View style={[styles.cell, styles.fechaColumn]}>
        <Text style={styles.cellText}>{formatDate(item.fechaTer)}</Text>
      </View>
      <View style={[styles.cell, styles.estatusColumn]}>
        <Text style={styles.cellText}>{getEstatusName(item.estatus_id)}</Text>
      </View>
      <View style={[styles.cell, styles.accionColumn]}>
        <View style={styles.actionButtons}>
          <TouchableOpacity style={styles.editButton} onPress={() => openEditModal(item)}>
            <MaterialCommunityIcons name="pencil" size={16} color="white" />
          </TouchableOpacity>
          <TouchableOpacity style={styles.deleteButton} onPress={() => handleDeleteEvento(item.id, item.nombre)}>
            <MaterialCommunityIcons name="delete" size={16} color="white" />
          </TouchableOpacity>
        </View>
      </View>
    </View>
  )

  const renderCreateModal = () => (
    <Modal visible={showCreateModal} animationType="slide" transparent>
      <View style={styles.modalOverlay}>
        <View style={styles.modalContent}>
          <View style={styles.modalHeader}>
            <Text style={styles.modalTitle}>Crear Nuevo Evento</Text>
            <TouchableOpacity
              onPress={() => {
                setShowCreateModal(false)
                resetForm()
              }}
            >
              <MaterialCommunityIcons name="close" size={24} color="#666" />
            </TouchableOpacity>
          </View>

          <ScrollView style={styles.modalBody}>
            <View style={styles.inputGroup}>
              <Text style={styles.inputLabel}>Nombre del evento</Text>
              <TextInput
                style={styles.textInput}
                value={formData.nombre}
                onChangeText={(text) => setFormData({ ...formData, nombre: text })}
                placeholder="Ingresa el nombre del evento"
              />
            </View>

            <View style={styles.inputGroup}>
              <Text style={styles.inputLabel}>Fecha de inicio (YYYY-MM-DD)</Text>
              <TextInput
                style={styles.textInput}
                value={formData.fechaIn}
                onChangeText={(text) => setFormData({ ...formData, fechaIn: text })}
                placeholder="2024-01-01"
              />
            </View>

            <View style={styles.inputGroup}>
              <Text style={styles.inputLabel}>Fecha de término (YYYY-MM-DD)</Text>
              <TextInput
                style={styles.textInput}
                value={formData.fechaTer}
                onChangeText={(text) => setFormData({ ...formData, fechaTer: text })}
                placeholder="2024-01-31"
              />
            </View>

            <View style={styles.inputGroup}>
              <Text style={styles.inputLabel}>Descripción</Text>
              <TextInput
                style={[styles.textInput, styles.textArea]}
                value={formData.descripcion}
                onChangeText={(text) => setFormData({ ...formData, descripcion: text })}
                placeholder="Describe el evento"
                multiline
                numberOfLines={4}
              />
            </View>

            <View style={styles.statusNote}>
              <MaterialCommunityIcons name="information" size={16} color="#007AFF" />
              <Text style={styles.statusNoteText}>El evento se creará con estatus "Activo" por defecto</Text>
            </View>
          </ScrollView>

          <View style={styles.modalFooter}>
            <TouchableOpacity
              style={styles.cancelButton}
              onPress={() => {
                setShowCreateModal(false)
                resetForm()
              }}
            >
              <Text style={styles.cancelButtonText}>Cancelar</Text>
            </TouchableOpacity>
            <TouchableOpacity style={styles.clearButton} onPress={resetForm}>
              <Text style={styles.clearButtonText}>Limpiar</Text>
            </TouchableOpacity>
            <TouchableOpacity style={styles.saveButton} onPress={handleCreateEvento}>
              <Text style={styles.saveButtonText}>Crear</Text>
            </TouchableOpacity>
          </View>
        </View>
      </View>
    </Modal>
  )

  const renderEditModal = () => (
    <Modal visible={showEditModal} animationType="slide" transparent>
      <View style={styles.modalOverlay}>
        <View style={styles.modalContent}>
          <View style={styles.modalHeader}>
            <Text style={styles.modalTitle}>Editar Evento</Text>
            <TouchableOpacity
              onPress={() => {
                setShowEditModal(false)
                setEditingEvento(null)
                resetForm()
              }}
            >
              <MaterialCommunityIcons name="close" size={24} color="#666" />
            </TouchableOpacity>
          </View>

          <ScrollView style={styles.modalBody}>
            <View style={styles.inputGroup}>
              <Text style={styles.inputLabel}>Nombre del evento</Text>
              <TextInput
                style={styles.textInput}
                value={formData.nombre}
                onChangeText={(text) => setFormData({ ...formData, nombre: text })}
                placeholder="Ingresa el nombre del evento"
              />
            </View>

            <View style={styles.inputGroup}>
              <Text style={styles.inputLabel}>Fecha de inicio (YYYY-MM-DD)</Text>
              <TextInput
                style={styles.textInput}
                value={formData.fechaIn}
                onChangeText={(text) => setFormData({ ...formData, fechaIn: text })}
                placeholder="2024-01-01"
              />
            </View>

            <View style={styles.inputGroup}>
              <Text style={styles.inputLabel}>Fecha de término (YYYY-MM-DD)</Text>
              <TextInput
                style={styles.textInput}
                value={formData.fechaTer}
                onChangeText={(text) => setFormData({ ...formData, fechaTer: text })}
                placeholder="2024-01-31"
              />
            </View>

            <View style={styles.inputGroup}>
              <Text style={styles.inputLabel}>Descripción</Text>
              <TextInput
                style={[styles.textInput, styles.textArea]}
                value={formData.descripcion}
                onChangeText={(text) => setFormData({ ...formData, descripcion: text })}
                placeholder="Describe el evento"
                multiline
                numberOfLines={4}
              />
            </View>

            <View style={styles.inputGroup}>
              <Text style={styles.inputLabel}>Estatus</Text>
              <View style={styles.pickerContainer}>
                {estatuses.map((estatus) => (
                  <TouchableOpacity
                    key={estatus.id}
                    style={[styles.pickerOption, formData.estatus_id === estatus.id && styles.pickerOptionSelected]}
                    onPress={() => setFormData({ ...formData, estatus_id: estatus.id })}
                  >
                    <Text
                      style={[
                        styles.pickerOptionText,
                        formData.estatus_id === estatus.id && styles.pickerOptionTextSelected,
                      ]}
                    >
                      {estatus.nombre}
                    </Text>
                  </TouchableOpacity>
                ))}
              </View>
            </View>
          </ScrollView>

          <View style={styles.modalFooter}>
            <TouchableOpacity
              style={styles.cancelButton}
              onPress={() => {
                setShowEditModal(false)
                setEditingEvento(null)
                resetForm()
              }}
            >
              <Text style={styles.cancelButtonText}>Cancelar</Text>
            </TouchableOpacity>
            <TouchableOpacity
              style={styles.clearButton}
              onPress={() => {
                if (editingEvento) {
                  setFormData({
                    nombre: editingEvento.nombre,
                    fechaIn: editingEvento.fechaIn,
                    fechaTer: editingEvento.fechaTer,
                    descripcion: editingEvento.descripcion,
                    estatus_id: editingEvento.estatus_id,
                  })
                }
              }}
            >
              <Text style={styles.clearButtonText}>Restaurar</Text>
            </TouchableOpacity>
            <TouchableOpacity style={styles.saveButton} onPress={handleUpdateEvento}>
              <Text style={styles.saveButtonText}>Actualizar</Text>
            </TouchableOpacity>
          </View>
        </View>
      </View>
    </Modal>
  )

  if (loading) {
    return (
      <SafeAreaView style={styles.loadingContainer}>
        <ActivityIndicator size="large" color="#007AFF" />
        <Text style={styles.loadingText}>Cargando eventos...</Text>
      </SafeAreaView>
    )
  }

  return (
    <SafeAreaView style={styles.container}>
      {/* Header */}
      <View style={styles.header}>
        <TouchableOpacity style={styles.backButton} onPress={() => router.back()}>
          <MaterialCommunityIcons name="arrow-left" size={24} color="#333" />
        </TouchableOpacity>
        <Text style={styles.title}>Administración de eventos</Text>
        <TouchableOpacity style={styles.addButton} onPress={() => setShowCreateModal(true)}>
          <MaterialCommunityIcons name="plus" size={24} color="#007AFF" />
        </TouchableOpacity>
      </View>

      <View style={styles.content}>
        {/* Table */}
        <View style={styles.table}>
          {/* Table Header */}
          <View style={styles.tableHeader}>
            <Text style={[styles.headerCell, styles.nombreColumn]}>Nombre</Text>
            <Text style={[styles.headerCell, styles.fechaColumn]}>Fecha Inicio</Text>
            <Text style={[styles.headerCell, styles.fechaColumn]}>Fecha Fin</Text>
            <Text style={[styles.headerCell, styles.estatusColumn]}>Estatus</Text>
            <Text style={[styles.headerCell, styles.accionColumn]}>Acción</Text>
          </View>

          {/* Table Body */}
          {getCurrentPageItems().length === 0 ? (
            <View style={styles.emptyContainer}>
              <MaterialCommunityIcons name="calendar-blank" size={64} color="#ccc" />
              <Text style={styles.emptyText}>No hay eventos registrados</Text>
              <Text style={styles.emptySubtext}>Crea tu primer evento usando el botón +</Text>
            </View>
          ) : (
            <FlatList
              data={getCurrentPageItems()}
              renderItem={renderEvento}
              keyExtractor={(item) => item.id.toString()}
              showsVerticalScrollIndicator={false}
              refreshControl={<RefreshControl refreshing={refreshing} onRefresh={onRefresh} />}
            />
          )}
        </View>

        {/* Pagination */}
        {eventos.length > 0 && (
          <View style={styles.pagination}>
            <TouchableOpacity
              style={[styles.paginationButton, currentPage === 1 && styles.disabledButton]}
              onPress={goToPreviousPage}
              disabled={currentPage === 1}
            >
              <Text style={[styles.paginationButtonText, currentPage === 1 && styles.disabledText]}>← Anterior</Text>
            </TouchableOpacity>

            <Text style={styles.pageInfo}>
              Página {currentPage} de {totalPages}
            </Text>

            <TouchableOpacity
              style={[styles.paginationButton, currentPage === totalPages && styles.disabledButton]}
              onPress={goToNextPage}
              disabled={currentPage === totalPages}
            >
              <Text style={[styles.paginationButtonText, currentPage === totalPages && styles.disabledText]}>
                Siguiente →
              </Text>
            </TouchableOpacity>
          </View>
        )}
      </View>

      {/* Modals */}
      {renderCreateModal()}
      {renderEditModal()}
    </SafeAreaView>
  )
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: "#f5f5f5",
  },
  loadingContainer: {
    flex: 1,
    justifyContent: "center",
    alignItems: "center",
    backgroundColor: "#f5f5f5",
  },
  loadingText: {
    marginTop: 10,
    fontSize: 16,
    color: "#666",
  },
  header: {
    flexDirection: "row",
    alignItems: "center",
    backgroundColor: "white",
    paddingHorizontal: 20,
    paddingVertical: 15,
    borderBottomWidth: 1,
    borderBottomColor: "#e0e0e0",
    paddingTop: 20,
  },
  backButton: {
    marginRight: 15,
    padding: 5,
  },
  title: {
    fontSize: 20,
    fontWeight: "bold",
    color: "#333",
    flex: 1,
  },
  addButton: {
    padding: 5,
  },
  content: {
    flex: 1,
    padding: 20,
  },
  table: {
    backgroundColor: "white",
    borderRadius: 8,
    overflow: "hidden",
    elevation: 2,
    shadowColor: "#000",
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    flex: 1,
  },
  tableHeader: {
    flexDirection: "row",
    backgroundColor: "#f8f9fa",
    borderBottomWidth: 1,
    borderBottomColor: "#e0e0e0",
    paddingVertical: 12,
  },
  headerCell: {
    fontSize: 14,
    fontWeight: "bold",
    color: "#333",
    textAlign: "center",
  },
  tableRow: {
    flexDirection: "row",
    borderBottomWidth: 1,
    borderBottomColor: "#e0e0e0",
    paddingVertical: 12,
    minHeight: 60,
  },
  cell: {
    justifyContent: "center",
    paddingHorizontal: 8,
  },
  cellText: {
    fontSize: 12,
    color: "#333",
    textAlign: "center",
  },
  nombreColumn: {
    flex: 3,
  },
  fechaColumn: {
    flex: 2,
  },
  estatusColumn: {
    flex: 1.5,
  },
  accionColumn: {
    flex: 1.5,
  },
  actionButtons: {
    flexDirection: "row",
    justifyContent: "center",
    alignItems: "center",
    gap: 5,
  },
  editButton: {
    backgroundColor: "#007AFF",
    borderRadius: 15,
    width: 30,
    height: 30,
    justifyContent: "center",
    alignItems: "center",
  },
  deleteButton: {
    backgroundColor: "#dc3545",
    borderRadius: 15,
    width: 30,
    height: 30,
    justifyContent: "center",
    alignItems: "center",
  },
  emptyContainer: {
    flex: 1,
    justifyContent: "center",
    alignItems: "center",
    padding: 40,
  },
  emptyText: {
    fontSize: 16,
    color: "#666",
    textAlign: "center",
    marginTop: 10,
    fontWeight: "500",
  },
  emptySubtext: {
    fontSize: 14,
    color: "#999",
    textAlign: "center",
    marginTop: 5,
  },
  pagination: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "center",
    marginTop: 20,
    paddingHorizontal: 10,
  },
  paginationButton: {
    backgroundColor: "#007AFF",
    paddingHorizontal: 15,
    paddingVertical: 8,
    borderRadius: 5,
  },
  paginationButtonText: {
    color: "white",
    fontSize: 14,
    fontWeight: "500",
  },
  disabledButton: {
    backgroundColor: "#ccc",
  },
  disabledText: {
    color: "#999",
  },
  pageInfo: {
    fontSize: 14,
    color: "#666",
  },
  modalOverlay: {
    flex: 1,
    backgroundColor: "rgba(0, 0, 0, 0.5)",
    justifyContent: "center",
    alignItems: "center",
  },
  modalContent: {
    backgroundColor: "white",
    borderRadius: 12,
    width: "90%",
    maxHeight: "80%",
  },
  modalHeader: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "center",
    padding: 20,
    borderBottomWidth: 1,
    borderBottomColor: "#e0e0e0",
  },
  modalTitle: {
    fontSize: 18,
    fontWeight: "bold",
    color: "#333",
  },
  modalBody: {
    padding: 20,
    maxHeight: 400,
  },
  inputGroup: {
    marginBottom: 20,
  },
  inputLabel: {
    fontSize: 16,
    fontWeight: "500",
    color: "#333",
    marginBottom: 8,
  },
  textInput: {
    borderWidth: 1,
    borderColor: "#e0e0e0",
    borderRadius: 8,
    paddingHorizontal: 12,
    paddingVertical: 10,
    fontSize: 16,
    backgroundColor: "#f9f9f9",
  },
  textArea: {
    height: 80,
    textAlignVertical: "top",
  },
  statusNote: {
    flexDirection: "row",
    alignItems: "center",
    backgroundColor: "#f0f8ff",
    padding: 12,
    borderRadius: 8,
    marginTop: 10,
  },
  statusNoteText: {
    fontSize: 14,
    color: "#007AFF",
    marginLeft: 8,
    flex: 1,
  },
  pickerContainer: {
    flexDirection: "row",
    flexWrap: "wrap",
    gap: 10,
  },
  pickerOption: {
    paddingHorizontal: 16,
    paddingVertical: 8,
    borderRadius: 20,
    borderWidth: 1,
    borderColor: "#e0e0e0",
    backgroundColor: "#f9f9f9",
  },
  pickerOptionSelected: {
    backgroundColor: "#007AFF",
    borderColor: "#007AFF",
  },
  pickerOptionText: {
    fontSize: 14,
    color: "#666",
  },
  pickerOptionTextSelected: {
    color: "white",
  },
  modalFooter: {
    flexDirection: "row",
    justifyContent: "space-between",
    padding: 20,
    borderTopWidth: 1,
    borderTopColor: "#e0e0e0",
    gap: 10,
  },
  cancelButton: {
    flex: 1,
    paddingVertical: 12,
    borderRadius: 8,
    borderWidth: 1,
    borderColor: "#e0e0e0",
    alignItems: "center",
  },
  cancelButtonText: {
    fontSize: 16,
    color: "#666",
  },
  clearButton: {
    flex: 1,
    paddingVertical: 12,
    borderRadius: 8,
    backgroundColor: "#f8f9fa",
    borderWidth: 1,
    borderColor: "#e0e0e0",
    alignItems: "center",
  },
  clearButtonText: {
    fontSize: 16,
    color: "#666",
  },
  saveButton: {
    flex: 1,
    paddingVertical: 12,
    borderRadius: 8,
    backgroundColor: "#007AFF",
    alignItems: "center",
  },
  saveButtonText: {
    fontSize: 16,
    color: "white",
    fontWeight: "500",
  },
})
