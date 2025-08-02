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
} from "react-native"
import { useRouter } from "expo-router"
import { MaterialCommunityIcons } from "@expo/vector-icons"
import AsyncStorage from "@react-native-async-storage/async-storage"
import { generateFastApiUrl } from "@/utils"

interface Usuario {
  id: number
  nombre: string
  aP: string
  aM: string
  correo: string
}

interface Articulo {
  id: number
  nombre: string
}

interface Unidad {
  id: number
  nombre: string
}

interface ArtPresentacion {
  id: number
  cantidad: number
  articulo: Articulo
  unidad: Unidad
}

interface Donacion {
  id: number
  tipo_donante: string
  fecha: string
  cantidad: number
  usuario_id: number
  articuloP_id: number
  estatus_id: number
  aprobacion: boolean
  del_flag: boolean
  usuario: Usuario
  articuloP: ArtPresentacion
}

export default function SolicitudesDonaciones() {
  const [donaciones, setDonaciones] = useState<Donacion[]>([])
  const [loading, setLoading] = useState(true)
  const [refreshing, setRefreshing] = useState(false)
  const [currentPage, setCurrentPage] = useState(1)
  const [totalPages, setTotalPages] = useState(1)
  const router = useRouter()
  const itemsPerPage = 10

  useEffect(() => {
    fetchDonaciones()
  }, [])

  const fetchDonaciones = async () => {
    try {
      const token = await AsyncStorage.getItem("access_token")
      if (!token) {
        Alert.alert("Sesión expirada", "Por favor inicia sesión nuevamente")
        router.replace("/login")
        return
      }

      const response = await fetch(generateFastApiUrl("/donaciones/"), {
        headers: {
          Authorization: `Bearer ${token}`,
          "Content-Type": "application/json",
        },
      })

      if (response.ok) {
        const data = await response.json()
        console.log("Donaciones data:", data) // Para debug

        // Filtrar donaciones pendientes (aprobacion = false y del_flag = false)
        const donacionesPendientes = data.filter((donacion: Donacion) => !donacion.aprobacion && !donacion.del_flag)

        setDonaciones(donacionesPendientes)
        setTotalPages(Math.ceil(donacionesPendientes.length / itemsPerPage))
      } else if (response.status === 401) {
        Alert.alert("Sesión expirada", "Por favor inicia sesión nuevamente")
        await AsyncStorage.removeItem("access_token")
        router.replace("/login")
      } else {
        const errorData = await response.json()
        console.error("Error response:", errorData)
        Alert.alert("Error", "No se pudieron cargar las donaciones")
      }
    } catch (error) {
      console.error("Error fetching donaciones:", error)
      Alert.alert("Error", "Error de conexión. Verifica tu conexión a internet.")
    } finally {
      setLoading(false)
      setRefreshing(false)
    }
  }

  const handleApprove = async (donacionId: number) => {
    Alert.alert("Confirmar Aprobación", "¿Estás seguro de que quieres aprobar esta donación?", [
      { text: "Cancelar", style: "cancel" },
      {
        text: "Aprobar",
        onPress: () => updateDonacionStatus(donacionId, true),
      },
    ])
  }

  const handleReject = async (donacionId: number) => {
    Alert.alert("Confirmar Rechazo", "¿Estás seguro de que quieres rechazar esta donación?", [
      { text: "Cancelar", style: "cancel" },
      {
        text: "Rechazar",
        style: "destructive",
        onPress: () => updateDonacionStatus(donacionId, false),
      },
    ])
  }

  const updateDonacionStatus = async (donacionId: number, aprobacion: boolean) => {
    try {
      const token = await AsyncStorage.getItem("access_token")
      if (!token) {
        Alert.alert("Sesión expirada", "Por favor inicia sesión nuevamente")
        router.replace("/login")
        return
      }

      // Intentar diferentes endpoints posibles
      let response = await fetch(generateFastApiUrl(`/donaciones/${donacionId}`), {
        method: "PUT",
        headers: {
          Authorization: `Bearer ${token}`,
          "Content-Type": "application/json",
        },
        body: JSON.stringify({ aprobacion: aprobacion }),
      })

      // Si no funciona, intentar con el endpoint de aprobar
      if (!response.ok) {
        response = await fetch(generateFastApiUrl(`/donaciones/${donacionId}/aprobar?aprobacion=${aprobacion}`), {
          method: "PUT",
          headers: {
            Authorization: `Bearer ${token}`,
            "Content-Type": "application/json",
          },
        })
      }

      if (response.ok) {
        Alert.alert("Éxito", `Donación ${aprobacion ? "aprobada" : "rechazada"} correctamente`)
        fetchDonaciones() // Recargar la lista
      } else if (response.status === 401) {
        Alert.alert("Sesión expirada", "Por favor inicia sesión nuevamente")
        await AsyncStorage.removeItem("access_token")
        router.replace("/login")
      } else {
        const errorData = await response.json()
        console.error("Error updating donacion:", errorData)
        Alert.alert("Error", "No se pudo actualizar el estado de la donación")
      }
    } catch (error) {
      console.error("Error updating donacion status:", error)
      Alert.alert("Error", "Error de conexión")
    }
  }

  const onRefresh = () => {
    setRefreshing(true)
    setCurrentPage(1)
    fetchDonaciones()
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

  const formatDonacionDetails = (donacion: Donacion) => {
    try {
      const articulo = donacion.articuloP?.articulo?.nombre || "Artículo"
      const unidad = donacion.articuloP?.unidad?.nombre || "unidad"
      const cantidad = donacion.cantidad || donacion.articuloP?.cantidad || 0
      return `${cantidad} ${articulo} (${unidad})`
    } catch (error) {
      return "Información no disponible"
    }
  }

  const formatDonanteName = (usuario: Usuario) => {
    if (!usuario) return "Usuario no disponible"
    return `${usuario.nombre || ""} ${usuario.aP || ""} ${usuario.aM || ""}`.trim() || usuario.correo || "Sin nombre"
  }

  const getCurrentPageItems = () => {
    const startIndex = (currentPage - 1) * itemsPerPage
    const endIndex = startIndex + itemsPerPage
    return donaciones.slice(startIndex, endIndex)
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

  const renderDonacion = ({ item }: { item: Donacion }) => (
    <View style={styles.tableRow}>
      <View style={[styles.cell, styles.donanteColumn]}>
        <Text style={styles.cellText} numberOfLines={2}>
          {formatDonanteName(item.usuario)}
        </Text>
      </View>
      <View style={[styles.cell, styles.donacionColumn]}>
        <Text style={styles.cellText} numberOfLines={2}>
          {formatDonacionDetails(item)}
        </Text>
      </View>
      <View style={[styles.cell, styles.fechaColumn]}>
        <Text style={styles.cellText}>{formatDate(item.fecha)}</Text>
      </View>
      <View style={[styles.cell, styles.accionColumn]}>
        <View style={styles.actionButtons}>
          <TouchableOpacity style={styles.approveButton} onPress={() => handleApprove(item.id)}>
            <MaterialCommunityIcons name="check" size={16} color="white" />
          </TouchableOpacity>
          <TouchableOpacity style={styles.rejectButton} onPress={() => handleReject(item.id)}>
            <MaterialCommunityIcons name="close" size={16} color="white" />
          </TouchableOpacity>
        </View>
      </View>
    </View>
  )

  if (loading) {
    return (
      <SafeAreaView style={styles.loadingContainer}>
        <ActivityIndicator size="large" color="#007AFF" />
        <Text style={styles.loadingText}>Cargando solicitudes de donaciones...</Text>
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
        <Text style={styles.title}>Solicitudes de donaciones</Text>
      </View>

      <View style={styles.content}>
        {/* Table */}
        <View style={styles.table}>
          {/* Table Header */}
          <View style={styles.tableHeader}>
            <Text style={[styles.headerCell, styles.donanteColumn]}>Donante</Text>
            <Text style={[styles.headerCell, styles.donacionColumn]}>Donación</Text>
            <Text style={[styles.headerCell, styles.fechaColumn]}>Fecha</Text>
            <Text style={[styles.headerCell, styles.accionColumn]}>Acción</Text>
          </View>

          {/* Table Body */}
          {getCurrentPageItems().length === 0 ? (
            <View style={styles.emptyContainer}>
              <MaterialCommunityIcons name="package-variant-closed" size={64} color="#ccc" />
              <Text style={styles.emptyText}>No hay solicitudes de donaciones pendientes</Text>
              <Text style={styles.emptySubtext}>Las nuevas solicitudes aparecerán aquí</Text>
            </View>
          ) : (
            <FlatList
              data={getCurrentPageItems()}
              renderItem={renderDonacion}
              keyExtractor={(item) => item.id.toString()}
              showsVerticalScrollIndicator={false}
              refreshControl={<RefreshControl refreshing={refreshing} onRefresh={onRefresh} />}
            />
          )}
        </View>

        {/* Pagination */}
        {donaciones.length > 0 && (
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
  donanteColumn: {
    flex: 2.5,
  },
  donacionColumn: {
    flex: 3,
  },
  fechaColumn: {
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
  approveButton: {
    backgroundColor: "#28a745",
    borderRadius: 15,
    width: 30,
    height: 30,
    justifyContent: "center",
    alignItems: "center",
  },
  rejectButton: {
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
})
