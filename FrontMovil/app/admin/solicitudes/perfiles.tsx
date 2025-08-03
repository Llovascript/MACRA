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
  rol_id: number
  aprobacion: boolean
  del: boolean
}

export default function SolicitudesPerfiles() {
  const [usuarios, setUsuarios] = useState<Usuario[]>([])
  const [loading, setLoading] = useState(true)
  const [refreshing, setRefreshing] = useState(false)
  const [currentPage, setCurrentPage] = useState(1)
  const [totalPages, setTotalPages] = useState(1)
  const router = useRouter()
  const itemsPerPage = 10

  useEffect(() => {
    fetchUsuarios()
  }, [])

  const fetchUsuarios = async () => {
    try {
      const token = await AsyncStorage.getItem("token")

      if (!token) {
        Alert.alert("Sesión expirada", "Por favor inicia sesión nuevamente")
        router.replace("/login")
        return
      }

      const response = await fetch(generateFastApiUrl("/usuarios/"), {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      })

      if (response.ok) {
        const data = await response.json()

        // Filtrar usuarios pendientes de aprobación y no eliminados
        const usuariosPendientes = data.filter((usuario: Usuario) => !usuario.aprobacion && !usuario.del)

        setUsuarios(usuariosPendientes)
        setTotalPages(Math.ceil(usuariosPendientes.length / itemsPerPage))
      } else if (response.status === 401) {
        Alert.alert("Sesión expirada", "Por favor inicia sesión nuevamente")
        await AsyncStorage.removeItem("token")
        router.replace("/login")
      } else {
        Alert.alert("Error", "No se pudieron cargar los usuarios")
      }
    } catch (error) {
      Alert.alert("Error", "Error de conexión al servidor")
    } finally {
      setLoading(false)
      setRefreshing(false)
    }
  }

  const handleApprove = async (usuario: Usuario) => {
    Alert.alert("Aprobar Usuario", `¿Estás seguro de que deseas aprobar a ${usuario.nombre} ${usuario.aP}?`, [
      { text: "Cancelar", style: "cancel" },
      {
        text: "Aprobar",
        onPress: async () => {
          try {
            const token = await AsyncStorage.getItem("token")

            const response = await fetch(generateFastApiUrl(`/usuarios/${usuario.id}`), {
              method: "PUT",
              headers: {
                "Content-Type": "application/json",
                Authorization: `Bearer ${token}`,
              },
              body: JSON.stringify({
                ...usuario,
                aprobacion: true,
              }),
            })

            if (response.ok) {
              Alert.alert("Éxito", "Usuario aprobado correctamente")
              fetchUsuarios() // Recargar la lista
            } else {
              Alert.alert("Error", "No se pudo aprobar el usuario")
            }
          } catch (error) {
            Alert.alert("Error", "Error de conexión")
          }
        },
      },
    ])
  }

  const handleReject = async (usuario: Usuario) => {
    Alert.alert(
      "Rechazar Usuario",
      `¿Estás seguro de que deseas rechazar a ${usuario.nombre} ${usuario.aP}? Esta acción no se puede deshacer.`,
      [
        { text: "Cancelar", style: "cancel" },
        {
          text: "Rechazar",
          style: "destructive",
          onPress: async () => {
            try {
              const token = await AsyncStorage.getItem("token")

              const response = await fetch(generateFastApiUrl(`/usuarios/${usuario.id}`), {
                method: "PUT",
                headers: {
                  "Content-Type": "application/json",
                  Authorization: `Bearer ${token}`,
                },
                body: JSON.stringify({
                  ...usuario,
                  del: true,
                }),
              })

              if (response.ok) {
                Alert.alert("Usuario Rechazado", "El usuario ha sido rechazado y eliminado de la lista")
                fetchUsuarios() // Recargar la lista
              } else {
                Alert.alert("Error", "No se pudo rechazar el usuario")
              }
            } catch (error) {
              Alert.alert("Error", "Error de conexión")
            }
          },
        },
      ],
    )
  }

  const getRoleName = (rolId: number) => {
    switch (rolId) {
      case 1:
        return "Administrador"
      case 2:
        return "Donante"
      case 3:
        return "Beneficiario"
      default:
        return "Desconocido"
    }
  }

  const onRefresh = () => {
    setRefreshing(true)
    setCurrentPage(1)
    fetchUsuarios()
  }

  const getCurrentPageItems = () => {
    const startIndex = (currentPage - 1) * itemsPerPage
    const endIndex = startIndex + itemsPerPage
    return usuarios.slice(startIndex, endIndex)
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

  const renderUsuario = ({ item }: { item: Usuario }) => (
    <View style={styles.tableRow}>
      <View style={[styles.cell, styles.nombreColumn]}>
        <Text style={styles.cellText} numberOfLines={2}>
          {`${item.nombre} ${item.aP} ${item.aM}`}
        </Text>
      </View>
      <View style={[styles.cell, styles.correoColumn]}>
        <Text style={styles.cellText} numberOfLines={2}>
          {item.correo}
        </Text>
      </View>
      <View style={[styles.cell, styles.rolColumn]}>
        <Text style={styles.cellText}>{getRoleName(item.rol_id)}</Text>
      </View>
      <View style={[styles.cell, styles.accionColumn]}>
        <View style={styles.actionButtons}>
          <TouchableOpacity style={styles.approveButton} onPress={() => handleApprove(item)}>
            <MaterialCommunityIcons name="check" size={16} color="white" />
          </TouchableOpacity>
          <TouchableOpacity style={styles.rejectButton} onPress={() => handleReject(item)}>
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
        <Text style={styles.loadingText}>Cargando solicitudes de perfiles...</Text>
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
        <Text style={styles.title}>Solicitudes de perfiles</Text>
      </View>

      <View style={styles.content}>
        {/* Table */}
        <View style={styles.table}>
          {/* Table Header */}
          <View style={styles.tableHeader}>
            <Text style={[styles.headerCell, styles.nombreColumn]}>Nombre</Text>
            <Text style={[styles.headerCell, styles.correoColumn]}>Correo electrónico</Text>
            <Text style={[styles.headerCell, styles.rolColumn]}>Rol</Text>
            <Text style={[styles.headerCell, styles.accionColumn]}>Acción</Text>
          </View>

          {/* Table Body */}
          {getCurrentPageItems().length === 0 ? (
            <View style={styles.emptyContainer}>
              <MaterialCommunityIcons name="account-check" size={64} color="#ccc" />
              <Text style={styles.emptyText}>No hay solicitudes de perfiles pendientes</Text>
              <Text style={styles.emptySubtext}>Las nuevas solicitudes aparecerán aquí</Text>
            </View>
          ) : (
            <FlatList
              data={getCurrentPageItems()}
              renderItem={renderUsuario}
              keyExtractor={(item) => item.id.toString()}
              showsVerticalScrollIndicator={false}
              refreshControl={<RefreshControl refreshing={refreshing} onRefresh={onRefresh} />}
            />
          )}
        </View>

        {/* Pagination */}
        {usuarios.length > 0 && (
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
  nombreColumn: {
    flex: 2.5,
  },
  correoColumn: {
    flex: 3,
  },
  rolColumn: {
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
