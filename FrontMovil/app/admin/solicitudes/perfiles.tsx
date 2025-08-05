"use client"

import { useState, useEffect } from "react"
import {
  View,
  Text,
  StyleSheet,
  TouchableOpacity,
  SafeAreaView,
  ScrollView,
  ActivityIndicator,
  Alert,
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
  telefono?: string
  edad?: number
  rfc?: string
  rol_id: number
  aprobacion: boolean | null
  del_flag: boolean
}

export default function AdminPerfilesScreen() {
  const [usuarios, setUsuarios] = useState<Usuario[]>([])
  const [loading, setLoading] = useState(true)
  const [refreshing, setRefreshing] = useState(false)
  const [processingId, setProcessingId] = useState<number | null>(null)
  const router = useRouter()

  useEffect(() => {
    loadUsuarios()
  }, [])

  const loadUsuarios = async () => {
    try {
      const token = await AsyncStorage.getItem("token")
      if (!token) {
        Alert.alert("Error", "No se encontró información de autenticación")
        router.replace("/login")
        return
      }

      const response = await fetch(generateFastApiUrl("/usuarios/"), {
        headers: {
          Authorization: `Bearer ${token}`,
          "Content-Type": "application/json",
        },
      })

      if (response.ok) {
        const data = await response.json()
        // Filtrar usuarios pendientes de aprobación (aprobacion === null o false) y no eliminados
        const usuariosPendientes = (data || []).filter(
          (usuario: Usuario) => (usuario.aprobacion === null || usuario.aprobacion === false) && !usuario.del_flag,
        )
        setUsuarios(usuariosPendientes)
      } else {
        Alert.alert("Error", "No se pudieron cargar los usuarios")
      }
    } catch (error) {
      console.error("Error loading usuarios:", error)
      Alert.alert("Error", "Error de conexión al cargar los usuarios")
    } finally {
      setLoading(false)
      setRefreshing(false)
    }
  }

  const onRefresh = () => {
    setRefreshing(true)
    loadUsuarios()
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
        return "Sin rol"
    }
  }

  const getRoleColor = (rolId: number) => {
    switch (rolId) {
      case 1:
        return "#FF5722"
      case 2:
        return "#4CAF50"
      case 3:
        return "#2196F3"
      default:
        return "#757575"
    }
  }

  const handleApproval = async (usuarioId: number, aprobar: boolean) => {
    setProcessingId(usuarioId)
    try {
      const token = await AsyncStorage.getItem("token")
      if (!token) {
        Alert.alert("Error", "No se encontró información de autenticación")
        return
      }

      const usuario = usuarios.find((u) => u.id === usuarioId)
      if (!usuario) return

      const response = await fetch(generateFastApiUrl(`/usuarios/${usuarioId}`), {
        method: "PUT",
        headers: {
          Authorization: `Bearer ${token}`,
          "Content-Type": "application/json",
        },
        body: JSON.stringify({
          ...usuario,
          aprobacion: aprobar,
          del_flag: aprobar ? 0 : 1, // Si se rechaza, se marca como eliminado
        }),
      })

      if (response.ok) {
        Alert.alert("¡Éxito!", `Usuario ${aprobar ? "aprobado" : "rechazado"} correctamente`)
        loadUsuarios() // Recargar la lista
      } else {
        const errorData = await response.text()
        console.error(`Error al ${aprobar ? "aprobar" : "rechazar"} usuario:`, errorData)
        Alert.alert("Error", `No se pudo ${aprobar ? "aprobar" : "rechazar"} el usuario`)
      }
    } catch (error) {
      console.error(`Error ${aprobar ? "approving" : "rejecting"} usuario:`, error)
      Alert.alert("Error", "Error de conexión")
    } finally {
      setProcessingId(null)
    }
  }

  const confirmApproval = (usuario: Usuario, aprobar: boolean) => {
    const action = aprobar ? "aprobar" : "rechazar"
    const userName = `${usuario.nombre} ${usuario.aP} ${usuario.aM}`.trim()

    Alert.alert(
      `¿${aprobar ? "Aprobar" : "Rechazar"} usuario?`,
      `${aprobar ? "Aprobarás" : "Rechazarás"} el perfil de:
      
• Nombre: ${userName}
• Correo: ${usuario.correo}
• Rol: ${getRoleName(usuario.rol_id)}
• Teléfono: ${usuario.telefono || "No especificado"}

Esta acción no se puede deshacer.`,
      [
        { text: "Cancelar", style: "cancel" },
        {
          text: aprobar ? "Aprobar" : "Rechazar",
          style: aprobar ? "default" : "destructive",
          onPress: () => handleApproval(usuario.id, aprobar),
        },
      ],
    )
  }

  const renderUsuarioItem = (usuario: Usuario) => {
    const userName = `${usuario.nombre} ${usuario.aP} ${usuario.aM}`.trim()
    const isProcessing = processingId === usuario.id

    return (
      <View key={usuario.id} style={styles.usuarioCard}>
        <View style={styles.cardHeader}>
          <View style={styles.userInfo}>
            <Text style={styles.userName}>{userName}</Text>
            <Text style={styles.userEmail}>{usuario.correo}</Text>
          </View>
          <View style={[styles.roleBadge, { backgroundColor: getRoleColor(usuario.rol_id) }]}>
            <Text style={styles.roleText}>{getRoleName(usuario.rol_id)}</Text>
          </View>
        </View>

        <View style={styles.cardBody}>
          {usuario.telefono && (
            <View style={styles.infoRow}>
              <MaterialCommunityIcons name="phone" size={16} color="#666" />
              <Text style={styles.infoText}>Teléfono: {usuario.telefono}</Text>
            </View>
          )}

          {usuario.edad && (
            <View style={styles.infoRow}>
              <MaterialCommunityIcons name="cake-variant" size={16} color="#666" />
              <Text style={styles.infoText}>Edad: {usuario.edad} años</Text>
            </View>
          )}

          {usuario.rfc && (
            <View style={styles.infoRow}>
              <MaterialCommunityIcons name="card-account-details" size={16} color="#666" />
              <Text style={styles.infoText}>RFC: {usuario.rfc}</Text>
            </View>
          )}

          <View style={styles.infoRow}>
            <MaterialCommunityIcons name="identifier" size={16} color="#666" />
            <Text style={styles.infoText}>ID: {usuario.id}</Text>
          </View>
        </View>

        <View style={styles.actionButtons}>


          <TouchableOpacity
            style={[styles.approveButton, isProcessing && styles.buttonDisabled]}
            onPress={() => confirmApproval(usuario, true)}
            disabled={isProcessing}
          >
            {isProcessing ? (
              <ActivityIndicator size="small" color="#fff" />
            ) : (
              <>
                <MaterialCommunityIcons name="check" size={18} color="#fff" />
                <Text style={styles.buttonText}>Aprobar</Text>
              </>
            )}
          </TouchableOpacity>
        </View>
      </View>
    )
  }

  if (loading) {
    return (
      <SafeAreaView style={styles.container}>
        <View style={styles.header}>
          <TouchableOpacity style={styles.backButton} onPress={() => router.back()}>
            <MaterialCommunityIcons name="arrow-left" size={24} color="#fff" />
          </TouchableOpacity>
          <Text style={styles.headerTitle}>Solicitudes de Perfiles</Text>
        </View>
        <View style={styles.loadingContainer}>
          <ActivityIndicator size="large" color="#8B4513" />
          <Text style={styles.loadingText}>Cargando solicitudes...</Text>
        </View>
      </SafeAreaView>
    )
  }

  return (
    <SafeAreaView style={styles.container}>
      <View style={styles.header}>
        <TouchableOpacity style={styles.backButton} onPress={() => router.back()}>
          <MaterialCommunityIcons name="arrow-left" size={24} color="#fff" />
        </TouchableOpacity>
        <Text style={styles.headerTitle}>Solicitudes de Perfiles</Text>
      </View>

      <ScrollView
        style={styles.content}
        showsVerticalScrollIndicator={false}
        refreshControl={<RefreshControl refreshing={refreshing} onRefresh={onRefresh} colors={["#8B4513"]} />}
      >
        <View style={styles.statsContainer}>
          <View style={styles.statCard}>
            <Text style={styles.statNumber}>{usuarios.length}</Text>
            <Text style={styles.statLabel}>Pendientes</Text>
          </View>
          <View style={styles.statCard}>
            <Text style={styles.statNumber}>{usuarios.filter((u) => u.rol_id === 2).length}</Text>
            <Text style={styles.statLabel}>Donantes</Text>
          </View>
          <View style={styles.statCard}>
            <Text style={styles.statNumber}>{usuarios.filter((u) => u.rol_id === 3).length}</Text>
            <Text style={styles.statLabel}>Beneficiarios</Text>
          </View>
        </View>

        <View style={styles.section}>
          <Text style={styles.sectionTitle}>Solicitudes Pendientes de Aprobación</Text>

          {usuarios.length === 0 ? (
            <View style={styles.emptyContainer}>
              <MaterialCommunityIcons name="account-check-outline" size={64} color="#ccc" />
              <Text style={styles.emptyTitle}>No hay solicitudes pendientes</Text>
              <Text style={styles.emptySubtitle}>Todos los perfiles han sido procesados</Text>
            </View>
          ) : (
            <View style={styles.usuariosList}>{usuarios.map(renderUsuarioItem)}</View>
          )}
        </View>
      </ScrollView>
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
  content: {
    flex: 1,
  },
  loadingContainer: {
    flex: 1,
    justifyContent: "center",
    alignItems: "center",
  },
  loadingText: {
    marginTop: 10,
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
  section: {
    padding: 20,
  },
  sectionTitle: {
    fontSize: 18,
    fontWeight: "bold",
    color: "#333",
    marginBottom: 15,
  },
  usuariosList: {
    gap: 15,
  },
  usuarioCard: {
    backgroundColor: "#fff",
    borderRadius: 12,
    padding: 16,
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
  userInfo: {
    flex: 1,
    marginRight: 10,
  },
  userName: {
    fontSize: 16,
    fontWeight: "bold",
    color: "#333",
    marginBottom: 2,
  },
  userEmail: {
    fontSize: 14,
    color: "#666",
  },
  roleBadge: {
    paddingHorizontal: 8,
    paddingVertical: 4,
    borderRadius: 12,
  },
  roleText: {
    fontSize: 12,
    fontWeight: "600",
    color: "#fff",
  },
  cardBody: {
    gap: 8,
    marginBottom: 16,
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
    gap: 12,
  },
  approveButton: {
    flex: 1,
    backgroundColor: "#4CAF50",
    borderRadius: 8,
    paddingVertical: 12,
    flexDirection: "row",
    justifyContent: "center",
    alignItems: "center",
    gap: 6,
  },
  rejectButton: {
    flex: 1,
    backgroundColor: "#F44336",
    borderRadius: 8,
    paddingVertical: 12,
    flexDirection: "row",
    justifyContent: "center",
    alignItems: "center",
    gap: 6,
  },
  buttonDisabled: {
    backgroundColor: "#ccc",
  },
  buttonText: {
    color: "#fff",
    fontSize: 14,
    fontWeight: "600",
  },
  emptyContainer: {
    alignItems: "center",
    paddingVertical: 40,
  },
  emptyTitle: {
    fontSize: 18,
    fontWeight: "bold",
    color: "#333",
    marginTop: 16,
  },
  emptySubtitle: {
    fontSize: 14,
    color: "#666",
    marginTop: 8,
  },
})
