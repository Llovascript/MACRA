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
  Platform,
} from "react-native"
import { useRouter } from "expo-router"
import { MaterialCommunityIcons } from "@expo/vector-icons"
import AsyncStorage from "@react-native-async-storage/async-storage"
import { generateFastApiUrl } from "@/utils"
import DateTimePicker from "@react-native-community/datetimepicker"

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

  // Estados para el selector de fechas
  const [showDatePicker, setShowDatePicker] = useState(false)
  const [datePickerMode, setDatePickerMode] = useState<"start" | "end">("start")
  const [tempDate, setTempDate] = useState(new Date())

  const router = useRouter()
  const itemsPerPage = 10

  // Helper function to safely convert error messages to strings
  const getErrorMessage = (error: any): string => {
    if (typeof error === "string") {
      return error
    }
    if (Array.isArray(error)) {
      return error.join(", ")
    }
    if (error && typeof error === "object") {
      return JSON.stringify(error)
    }
    return "Error desconocido"
  }

  // Helper function to format date for API (YYYY-MM-DD)
  const formatDateForAPI = (date: Date): string => {
    const year = date.getFullYear()
    const month = String(date.getMonth() + 1).padStart(2, "0")
    const day = String(date.getDate()).padStart(2, "0")
    return `${year}-${month}-${day}`
  }

  // Helper function to parse date from API
  const parseDateFromAPI = (dateString: string): Date => {
    const [year, month, day] = dateString.split("-").map(Number)
    return new Date(year, month - 1, day)
  }

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
      setEstatuses([
        { id: 1, nombre: "Activo" },
        { id: 2, nombre: "Inactivo" },
      ])
    } catch (error) {
      console.error("Error fetching estatuses:", error)
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
        try {
          const errorData = await response.json()
          const errorMessage = getErrorMessage(errorData.detail || errorData.message || errorData)
          Alert.alert("Error", errorMessage)
        } catch (parseError) {
          Alert.alert("Error", "No se pudo crear el evento")
        }
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
        try {
          const errorData = await response.json()
          const errorMessage = getErrorMessage(errorData.detail || errorData.message || errorData)
          Alert.alert("Error", errorMessage)
        } catch (parseError) {
          Alert.alert("Error", "No se pudo actualizar el evento")
        }
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
              try {
                const errorData = await response.json()
                const errorMessage = getErrorMessage(errorData.detail || errorData.message || errorData)
                Alert.alert("Error", errorMessage)
              } catch (parseError) {
                Alert.alert("Error", "No se pudo eliminar el evento")
              }
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
      const [year, month, day] = dateString.split("-").map(Number)
      const date = new Date(year, month - 1, day)
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
    return estatus?.nombre || "Activo"
  }

  const getEventStatus = (fechaIn: string, fechaTer: string) => {
    const now = new Date()
    const inicio = new Date(fechaIn)
    const fin = new Date(fechaTer)

    if (now < inicio) {
      return { status: "Próximo", color: "#2196F3", icon: "calendar-clock" }
    } else if (now >= inicio && now <= fin) {
      return { status: "En curso", color: "#4CAF50", icon: "calendar-check" }
    } else {
      return { status: "Finalizado", color: "#757575", icon: "calendar-remove" }
    }
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

  // Funciones para el selector de fechas
  const openDatePicker = (mode: "start" | "end") => {
    setDatePickerMode(mode)
    if (mode === "start" && formData.fechaIn) {
      setTempDate(parseDateFromAPI(formData.fechaIn))
    } else if (mode === "end" && formData.fechaTer) {
      setTempDate(parseDateFromAPI(formData.fechaTer))
    } else {
      setTempDate(new Date())
    }
    setShowDatePicker(true)
  }

  const onDateChange = (event: any, selectedDate?: Date) => {
    setShowDatePicker(false)

    if (selectedDate && event.type !== "dismissed") {
      const formattedDate = formatDateForAPI(selectedDate)
      if (datePickerMode === "start") {
        setFormData({ ...formData, fechaIn: formattedDate })
      } else {
        setFormData({ ...formData, fechaTer: formattedDate })
      }
    }
  }

  const confirmDateSelection = () => {
    setShowDatePicker(false)
  }

  const renderEvento = ({ item }: { item: Evento }) => {
    const eventStatus = getEventStatus(item.fechaIn, item.fechaTer)

    return (
      <View style={styles.eventoCard}>
        <View style={styles.cardHeader}>
          <View style={styles.eventoInfo}>
            <Text style={styles.eventoNombre}>{item.nombre}</Text>
            <Text style={styles.eventoDescripcion} numberOfLines={2}>
              {item.descripcion}
            </Text>
          </View>
          <View style={styles.cardActions}>
            <View style={[styles.statusBadge, { backgroundColor: eventStatus.color }]}>
              <MaterialCommunityIcons name={eventStatus.icon as any} size={14} color="#fff" />
              <Text style={styles.statusText}>{eventStatus.status}</Text>
            </View>
          </View>
        </View>

        <View style={styles.cardBody}>
          <View style={styles.dateRow}>
            <View style={styles.dateItem}>
              <MaterialCommunityIcons name="calendar-start" size={16} color="#666" />
              <Text style={styles.dateLabel}>Inicio:</Text>
              <Text style={styles.dateValue}>{formatDate(item.fechaIn)}</Text>
            </View>
            <View style={styles.dateItem}>
              <MaterialCommunityIcons name="calendar-end" size={16} color="#666" />
              <Text style={styles.dateLabel}>Fin:</Text>
              <Text style={styles.dateValue}>{formatDate(item.fechaTer)}</Text>
            </View>
          </View>

          <View style={styles.infoRow}>
            <MaterialCommunityIcons name="identifier" size={16} color="#666" />
            <Text style={styles.infoText}>ID: {item.id}</Text>
            <Text style={styles.infoText}>• Estatus: {getEstatusName(item.estatus_id)}</Text>
          </View>

          <View style={styles.actionButtons}>
            <TouchableOpacity style={styles.editButton} onPress={() => openEditModal(item)}>
              <MaterialCommunityIcons name="pencil" size={16} color="white" />
              <Text style={styles.actionButtonText}>Editar</Text>
            </TouchableOpacity>
            <TouchableOpacity style={styles.deleteButton} onPress={() => handleDeleteEvento(item.id, item.nombre)}>
              <MaterialCommunityIcons name="delete" size={16} color="white" />
              <Text style={styles.actionButtonText}>Eliminar</Text>
            </TouchableOpacity>
          </View>
        </View>
      </View>
    )
  }

  const renderDateInput = (label: string, value: string, mode: "start" | "end") => (
    <View style={styles.inputGroup}>
      <Text style={styles.inputLabel}>{label}</Text>
      <TouchableOpacity style={styles.dateInput} onPress={() => openDatePicker(mode)}>
        <MaterialCommunityIcons name="calendar" size={20} color="#666" />
        <Text style={[styles.dateInputText, !value && styles.placeholderText]}>
          {value ? formatDate(value) : "Seleccionar fecha"}
        </Text>
        <MaterialCommunityIcons name="chevron-down" size={20} color="#666" />
      </TouchableOpacity>
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

            {renderDateInput("Fecha de inicio", formData.fechaIn, "start")}
            {renderDateInput("Fecha de término", formData.fechaTer, "end")}

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

      {/* Date Picker */}
      {showDatePicker && (
        <DateTimePicker
          value={tempDate}
          mode="date"
          display={Platform.OS === "ios" ? "spinner" : "default"}
          onChange={onDateChange}
          minimumDate={new Date()}
        />
      )}
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

            {renderDateInput("Fecha de inicio", formData.fechaIn, "start")}
            {renderDateInput("Fecha de término", formData.fechaTer, "end")}

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

      {/* Date Picker para Edit Modal */}
      {showDatePicker && (
        <DateTimePicker
          value={tempDate}
          mode="date"
          display={Platform.OS === "ios" ? "spinner" : "default"}
          onChange={onDateChange}
          minimumDate={new Date()}
        />
      )}
    </Modal>
  )

  if (loading) {
    return (
      <SafeAreaView style={styles.container}>
        <View style={styles.header}>
          <TouchableOpacity style={styles.backButton} onPress={() => router.back()}>
            <MaterialCommunityIcons name="arrow-left" size={24} color="#fff" />
          </TouchableOpacity>
          <Text style={styles.headerTitle}>Gestión de Eventos</Text>
        </View>
        <View style={styles.loadingContainer}>
          <ActivityIndicator size="large" color="#8B4513" />
          <Text style={styles.loadingText}>Cargando eventos...</Text>
        </View>
      </SafeAreaView>
    )
  }

  const eventosActivos = eventos
  const eventosProximos = eventosActivos.filter((e) => new Date() < new Date(e.fechaIn)).length
  const eventosEnCurso = eventosActivos.filter((e) => {
    const now = new Date()
    return now >= new Date(e.fechaIn) && now <= new Date(e.fechaTer)
  }).length
  const eventosFinalizados = eventosActivos.filter((e) => new Date() > new Date(e.fechaTer)).length

  return (
    <SafeAreaView style={styles.container}>
      {/* Header */}
      <View style={styles.header}>
        <TouchableOpacity style={styles.backButton} onPress={() => router.back()}>
          <MaterialCommunityIcons name="arrow-left" size={24} color="#fff" />
        </TouchableOpacity>
        <Text style={styles.headerTitle}>Gestión de Eventos</Text>
        <TouchableOpacity style={styles.addButton} onPress={() => setShowCreateModal(true)}>
          <MaterialCommunityIcons name="plus" size={24} color="#fff" />
        </TouchableOpacity>
      </View>

      {/* Estadísticas */}
      <View style={styles.statsContainer}>
        <View style={styles.statCard}>
          <Text style={styles.statNumber}>{eventosActivos.length}</Text>
          <Text style={styles.statLabel}>Total</Text>
        </View>
        <View style={styles.statCard}>
          <Text style={[styles.statNumber, { color: "#2196F3" }]}>{eventosProximos}</Text>
          <Text style={styles.statLabel}>Próximos</Text>
        </View>
        <View style={styles.statCard}>
          <Text style={[styles.statNumber, { color: "#4CAF50" }]}>{eventosEnCurso}</Text>
          <Text style={styles.statLabel}>En Curso</Text>
        </View>
        <View style={styles.statCard}>
          <Text style={[styles.statNumber, { color: "#757575" }]}>{eventosFinalizados}</Text>
          <Text style={styles.statLabel}>Finalizados</Text>
        </View>
      </View>

      <View style={styles.content}>
        {/* Lista de Eventos */}
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
            refreshControl={<RefreshControl refreshing={refreshing} onRefresh={onRefresh} colors={["#8B4513"]} />}
            contentContainerStyle={styles.listContainer}
          />
        )}

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
  header: {
    backgroundColor: "#8B4513",
    flexDirection: "row",
    alignItems: "center",
    paddingHorizontal: 16,
    paddingTop: 50,
    paddingBottom: 16,
  },
  backButton: {
    padding: 8,
    marginRight: 8,
  },
  headerTitle: {
    fontSize: 20,
    fontWeight: "bold",
    color: "#fff",
    flex: 1,
  },
  addButton: {
    padding: 8,
  },
  loadingContainer: {
    flex: 1,
    justifyContent: "center",
    alignItems: "center",
  },
  loadingText: {
    marginTop: 12,
    fontSize: 16,
    color: "#666",
  },
  statsContainer: {
    flexDirection: "row",
    padding: 20,
    gap: 10,
  },
  statCard: {
    flex: 1,
    backgroundColor: "#fff",
    borderRadius: 8,
    padding: 15,
    alignItems: "center",
    shadowColor: "#000",
    shadowOffset: { width: 0, height: 1 },
    shadowOpacity: 0.1,
    shadowRadius: 2,
    elevation: 2,
  },
  statNumber: {
    fontSize: 24,
    fontWeight: "bold",
    color: "#8B4513",
  },
  statLabel: {
    fontSize: 12,
    color: "#666",
    marginTop: 4,
  },
  content: {
    flex: 1,
    paddingHorizontal: 20,
  },
  listContainer: {
    paddingBottom: 20,
  },
  eventoCard: {
    backgroundColor: "#fff",
    borderRadius: 12,
    padding: 16,
    marginBottom: 12,
    shadowColor: "#000",
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 3,
  },
  cardHeader: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "flex-start",
    marginBottom: 12,
  },
  eventoInfo: {
    flex: 1,
    marginRight: 12,
  },
  eventoNombre: {
    fontSize: 18,
    fontWeight: "bold",
    color: "#333",
    marginBottom: 4,
  },
  eventoDescripcion: {
    fontSize: 14,
    color: "#666",
    lineHeight: 20,
  },
  cardActions: {
    alignItems: "flex-end",
  },
  statusBadge: {
    flexDirection: "row",
    alignItems: "center",
    paddingHorizontal: 8,
    paddingVertical: 4,
    borderRadius: 12,
    gap: 4,
  },
  statusText: {
    color: "#fff",
    fontSize: 12,
    fontWeight: "600",
  },
  cardBody: {
    gap: 12,
  },
  dateRow: {
    flexDirection: "row",
    justifyContent: "space-between",
  },
  dateItem: {
    flexDirection: "row",
    alignItems: "center",
    gap: 4,
    flex: 1,
  },
  dateLabel: {
    fontSize: 12,
    color: "#999",
  },
  dateValue: {
    fontSize: 14,
    fontWeight: "600",
    color: "#333",
  },
  infoRow: {
    flexDirection: "row",
    alignItems: "center",
    gap: 8,
  },
  infoText: {
    fontSize: 14,
    color: "#666",
  },
  actionButtons: {
    flexDirection: "row",
    gap: 8,
    marginTop: 8,
  },
  editButton: {
    backgroundColor: "#007AFF",
    flexDirection: "row",
    alignItems: "center",
    paddingHorizontal: 12,
    paddingVertical: 8,
    borderRadius: 6,
    gap: 4,
    flex: 1,
    justifyContent: "center",
  },
  deleteButton: {
    backgroundColor: "#dc3545",
    flexDirection: "row",
    alignItems: "center",
    paddingHorizontal: 12,
    paddingVertical: 8,
    borderRadius: 6,
    gap: 4,
    flex: 1,
    justifyContent: "center",
  },
  actionButtonText: {
    color: "#fff",
    fontSize: 12,
    fontWeight: "600",
  },
  emptyContainer: {
    flex: 1,
    justifyContent: "center",
    alignItems: "center",
    paddingVertical: 40,
  },
  emptyText: {
    fontSize: 18,
    fontWeight: "600",
    color: "#333",
    marginTop: 16,
    marginBottom: 8,
  },
  emptySubtext: {
    fontSize: 14,
    color: "#666",
    textAlign: "center",
  },
  pagination: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "center",
    marginTop: 20,
    paddingHorizontal: 10,
    paddingBottom: 20,
  },
  paginationButton: {
    backgroundColor: "#8B4513",
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
  dateInput: {
    flexDirection: "row",
    alignItems: "center",
    borderWidth: 1,
    borderColor: "#e0e0e0",
    borderRadius: 8,
    paddingHorizontal: 12,
    paddingVertical: 12,
    backgroundColor: "#f9f9f9",
    gap: 8,
  },
  dateInputText: {
    flex: 1,
    fontSize: 16,
    color: "#333",
  },
  placeholderText: {
    color: "#999",
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
