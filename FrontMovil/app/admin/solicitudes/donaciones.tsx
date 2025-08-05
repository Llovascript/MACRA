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
  aP?: string
  aM?: string
  correo: string
  telefono?: string
}

interface ArticuloPresentacion {
  id: number
  articulo?: {
    id: number
    nombre: string
    categoria?: {
      id: number
      nombre: string
    }
  }
  unidad_medida?: {
    id: number
    nombre: string
  }
  cantidad_presentacion?: number
}

interface Donacion {
  id: number
  tipo_donante: string | null
  fecha: string
  cantidad: number
  aprobacion: boolean | null
  articuloP_id: number
  estatus_id: number
  usuario_id: number
  usuario?: Usuario
  articulo_presentacion?: ArticuloPresentacion
}

interface EstadisticasDonaciones {
  total: number
  pendientes: number
  aprobadas: number
  rechazadas: number
}

export default function AdminDonacionesScreen() {
  const [donaciones, setDonaciones] = useState<Donacion[]>([])
  const [estadisticas, setEstadisticas] = useState<EstadisticasDonaciones>({
    total: 0,
    pendientes: 0,
    aprobadas: 0,
    rechazadas: 0,
  })
  const [usuarios, setUsuarios] = useState<{ [key: number]: Usuario }>({})
  const [loading, setLoading] = useState(true)
  const [refreshing, setRefreshing] = useState(false)
  const [processingId, setProcessingId] = useState<number | null>(null)
  const router = useRouter()

  // Datos de respaldo para cuando no se puedan cargar desde la API
  const presentacionesBackup: { [key: number]: { nombre: string; unidad: string; categoria: string } } = {
    1: { nombre: "Arroz", unidad: "Kilogramo", categoria: "Alimentos" },
    2: { nombre: "Frijoles", unidad: "Kilogramo", categoria: "Alimentos" },
    3: { nombre: "Aceite", unidad: "Litro", categoria: "Alimentos" },
    4: { nombre: "Leche", unidad: "Litro", categoria: "Alimentos" },
    5: { nombre: "Huevos", unidad: "Piezas", categoria: "Alimentos" },
    6: { nombre: "Pan", unidad: "Pieza", categoria: "Alimentos" },
    7: { nombre: "Azúcar", unidad: "Kilogramo", categoria: "Alimentos" },
    8: { nombre: "Sal", unidad: "Kilogramo", categoria: "Alimentos" },
    9: { nombre: "Pasta", unidad: "Gramos", categoria: "Alimentos" },
    10: { nombre: "Atún", unidad: "Lata", categoria: "Alimentos" },
    11: { nombre: "Jabón", unidad: "Pieza", categoria: "Limpieza" },
    12: { nombre: "Detergente", unidad: "Litro", categoria: "Limpieza" },
    13: { nombre: "Papel Higiénico", unidad: "Rollos", categoria: "Higiene" },
    14: { nombre: "Pasta de Dientes", unidad: "Tubo", categoria: "Higiene" },
    15: { nombre: "Shampoo", unidad: "Botella", categoria: "Higiene" },
  }

  useEffect(() => {
    loadData()
  }, [])

  const loadUsuarios = async (token: string) => {
    try {
      const response = await fetch(generateFastApiUrl("/usuarios/"), {
        headers: {
          Authorization: `Bearer ${token}`,
          "Content-Type": "application/json",
        },
      })

      if (response.ok) {
        const usuariosData = await response.json()
        const usuariosMap: { [key: number]: Usuario } = {}

        usuariosData.forEach((usuario: Usuario) => {
          usuariosMap[usuario.id] = usuario
        })

        setUsuarios(usuariosMap)
        console.log("Usuarios cargados:", Object.keys(usuariosMap).length)
      } else {
        console.error("Error al cargar usuarios:", response.status)
      }
    } catch (error) {
      console.error("Error loading usuarios:", error)
    }
  }

  const loadEstadisticas = async (token: string) => {
    try {
      const response = await fetch(generateFastApiUrl("/admin/donaciones/estadisticas"), {
        headers: {
          Authorization: `Bearer ${token}`,
          "Content-Type": "application/json",
        },
      })

      if (response.ok) {
        const stats = await response.json()
        setEstadisticas(stats)
        console.log("Estadísticas cargadas:", stats)
      } else {
        console.log("No se pudieron cargar estadísticas, usando valores por defecto")
      }
    } catch (error) {
      console.error("Error loading estadisticas:", error)
    }
  }

  const loadDonaciones = async (token: string) => {
    try {
      console.log("Cargando donaciones pendientes...")
      const response = await fetch(generateFastApiUrl("/donaciones/"), {
        headers: {
          Authorization: `Bearer ${token}`,
          "Content-Type": "application/json",
        },
      })

      if (response.ok) {
        const data = await response.json()
        console.log("Donaciones cargadas:", JSON.stringify(data, null, 2))

        // Filtrar donaciones pendientes
        const donacionesPendientes = (data || []).filter((d: Donacion) => {
          return d.aprobacion === null || d.aprobacion === false || d.aprobacion === undefined
        })

        console.log("Donaciones después del filtro:", donacionesPendientes)
        console.log("Cantidad de donaciones pendientes:", donacionesPendientes.length)

        setDonaciones(donacionesPendientes)
      } else {
        console.error("Error al cargar donaciones:", response.status)
        const errorText = await response.text()
        console.error("Error details:", errorText)
        Alert.alert("Error", "No se pudieron cargar las donaciones")
      }
    } catch (error) {
      console.error("Error loading donaciones:", error)
      Alert.alert("Error", "Error de conexión al cargar las donaciones")
    }
  }

  const loadData = async () => {
    try {
      const token = await AsyncStorage.getItem("token")
      if (!token) {
        Alert.alert("Error", "No se encontró información de autenticación")
        router.replace("/login")
        return
      }

      // Cargar datos en paralelo
      await Promise.all([loadUsuarios(token), loadDonaciones(token), loadEstadisticas(token)])
    } catch (error) {
      console.error("Error loading data:", error)
      Alert.alert("Error", "Error de conexión")
    } finally {
      setLoading(false)
      setRefreshing(false)
    }
  }

  const onRefresh = () => {
    setRefreshing(true)
    loadData()
  }

  const getUsuarioInfo = (usuarioId: number) => {
    const usuario = usuarios[usuarioId]
    if (usuario) {
      const nombreCompleto = `${usuario.nombre} ${usuario.aP || ""} ${usuario.aM || ""}`.trim()
      return {
        nombre: nombreCompleto,
        correo: usuario.correo,
        telefono: usuario.telefono,
      }
    }

    return {
      nombre: `Usuario ID ${usuarioId}`,
      correo: "Sin correo",
      telefono: null,
    }
  }

  const getArticuloInfo = (donacion: Donacion) => {
    // Primero intentar usar los datos de la API
    if (donacion.articulo_presentacion?.articulo) {
      const articulo = donacion.articulo_presentacion.articulo
      const categoria = articulo.categoria?.nombre || "Sin categoría"
      const unidad = donacion.articulo_presentacion.unidad_medida?.nombre || "Unidad"

      return {
        nombre: articulo.nombre,
        unidad: unidad,
        categoria: categoria,
        source: "api",
      }
    }

    // Si no hay datos de la API, usar datos de respaldo
    const backup = presentacionesBackup[donacion.articuloP_id]
    if (backup) {
      return {
        nombre: backup.nombre,
        unidad: backup.unidad,
        categoria: backup.categoria,
        source: "backup",
      }
    }

    // Si no hay datos en ningún lado, mostrar información genérica
    return {
      nombre: `Artículo ID ${donacion.articuloP_id}`,
      unidad: "Unidad",
      categoria: "Sin categoría",
      source: "generic",
    }
  }

  const formatDate = (dateString: string) => {
    try {
      const date = new Date(dateString)
      return date.toLocaleDateString("es-ES", {
        year: "numeric",
        month: "long",
        day: "numeric",
      })
    } catch {
      return dateString
    }
  }

  const handleApproval = async (donacionId: number, aprobar: boolean) => {
    setProcessingId(donacionId)
    try {
      const token = await AsyncStorage.getItem("token")
      if (!token) {
        Alert.alert("Error", "No se encontró información de autenticación")
        return
      }

      const action = aprobar ? "aprobar" : "rechazar"
      console.log(`${action} donación ${donacionId} con aprobacion: ${aprobar}`)

      // Usar el endpoint correcto con método PUT y parámetro aprobacion
      const response = await fetch(generateFastApiUrl(`/donaciones/${donacionId}/aprobar?aprobacion=${aprobar}`), {
        method: "PUT",
        headers: {
          Authorization: `Bearer ${token}`,
          "Content-Type": "application/json",
        },
      })

      console.log("Respuesta del servidor:", response.status)

      if (response.ok) {
        const responseData = await response.json()
        console.log("Donación actualizada:", responseData)
        Alert.alert("¡Éxito!", `Donación ${aprobar ? "aprobada" : "rechazada"} correctamente`)
        loadData() // Recargar todos los datos
      } else {
        const errorData = await response.text()
        console.error(`Error al ${action} donación:`, errorData)
        Alert.alert("Error", `No se pudo ${action} la donación: ${errorData}`)
      }
    } catch (error) {
      console.error(`Error ${aprobar ? "approving" : "rejecting"} donacion:`, error)
      Alert.alert("Error", "Error de conexión")
    } finally {
      setProcessingId(null)
    }
  }

  const confirmApproval = (donacion: Donacion, aprobar: boolean) => {
    const articuloInfo = getArticuloInfo(donacion)
    const usuarioInfo = getUsuarioInfo(donacion.usuario_id)
    const action = aprobar ? "aprobar" : "rechazar"

    Alert.alert(
      `¿${aprobar ? "Aprobar" : "Rechazar"} donación?`,
      `${aprobar ? "Aprobarás" : "Rechazarás"} la donación de:
      
• Donante: ${usuarioInfo.nombre}
• Correo: ${usuarioInfo.correo}
• Artículo: ${articuloInfo.nombre}
• Cantidad: ${donacion.cantidad} ${articuloInfo.unidad}
• Fecha: ${formatDate(donacion.fecha)}

Esta acción no se puede deshacer.`,
      [
        { text: "Cancelar", style: "cancel" },
        {
          text: aprobar ? "Aprobar" : "Rechazar",
          style: aprobar ? "default" : "destructive",
          onPress: () => handleApproval(donacion.id, aprobar),
        },
      ],
    )
  }

  const renderDonacionItem = (donacion: Donacion) => {
    const articuloInfo = getArticuloInfo(donacion)
    const usuarioInfo = getUsuarioInfo(donacion.usuario_id)
    const isProcessing = processingId === donacion.id

    return (
      <View key={donacion.id} style={styles.donacionCard}>
        <View style={styles.cardHeader}>
          <View style={styles.donorInfo}>
            <Text style={styles.donorName}>{usuarioInfo.nombre}</Text>
            <Text style={styles.donorEmail}>{usuarioInfo.correo}</Text>
            {usuarioInfo.telefono && <Text style={styles.donorPhone}>📞 {usuarioInfo.telefono}</Text>}
          </View>
          <View style={styles.pendingBadge}>
            <MaterialCommunityIcons name="clock" size={16} color="#FF9800" />
            <Text style={styles.pendingText}>Pendiente</Text>
          </View>
        </View>

        <View style={styles.cardBody}>
          <View style={styles.articleSection}>
            <Text style={styles.articleName}>{articuloInfo.nombre}</Text>
            <Text style={styles.articleDetails}>
              {donacion.cantidad} {articuloInfo.unidad} • {articuloInfo.categoria}
            </Text>
            {articuloInfo.source !== "api" && (
              <Text style={styles.sourceWarning}>
                {articuloInfo.source === "backup" ? "📋 Datos locales" : "⚠️ Datos genéricos"}
              </Text>
            )}
          </View>

          <View style={styles.infoRow}>
            <MaterialCommunityIcons name="calendar" size={16} color="#666" />
            <Text style={styles.infoText}>Fecha: {formatDate(donacion.fecha)}</Text>
          </View>

          <View style={styles.infoRow}>
            <MaterialCommunityIcons name="identifier" size={16} color="#666" />
            <Text style={styles.infoText}>ID: {donacion.id}</Text>
          </View>
        </View>

        <View style={styles.actionButtons}>

          <TouchableOpacity
            style={[styles.approveButton, isProcessing && styles.buttonDisabled]}
            onPress={() => confirmApproval(donacion, true)}
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
          <Text style={styles.headerTitle}>Solicitudes de Donación</Text>
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
        <Text style={styles.headerTitle}>Solicitudes de Donación</Text>
      </View>

      <ScrollView
        style={styles.content}
        showsVerticalScrollIndicator={false}
        refreshControl={<RefreshControl refreshing={refreshing} onRefresh={onRefresh} colors={["#8B4513"]} />}
      >
        <View style={styles.statsContainer}>
          <View style={styles.statCard}>
            <Text style={styles.statNumber}>{estadisticas.pendientes || donaciones.length}</Text>
            <Text style={styles.statLabel}>Pendientes</Text>
          </View>
          <View style={styles.statCard}>
            <Text style={styles.statNumber}>{estadisticas.aprobadas || 0}</Text>
            <Text style={styles.statLabel}>Aprobadas</Text>
          </View>
          <View style={styles.statCard}>
            <Text style={styles.statNumber}>{estadisticas.total || donaciones.length}</Text>
            <Text style={styles.statLabel}>Total</Text>
          </View>
        </View>

        <View style={styles.section}>
          <Text style={styles.sectionTitle}>Solicitudes Pendientes de Aprobación</Text>

          {donaciones.length === 0 ? (
            <View style={styles.emptyContainer}>
              <MaterialCommunityIcons name="gift-outline" size={64} color="#ccc" />
              <Text style={styles.emptyTitle}>No hay solicitudes pendientes</Text>
              <Text style={styles.emptySubtitle}>Todas las donaciones han sido procesadas</Text>
            </View>
          ) : (
            <View style={styles.donacionesList}>{donaciones.map(renderDonacionItem)}</View>
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
    color: "#FF9800",
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
  donacionesList: {
    gap: 15,
  },
  donacionCard: {
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
  donorInfo: {
    flex: 1,
    marginRight: 10,
  },
  donorName: {
    fontSize: 16,
    fontWeight: "bold",
    color: "#333",
    marginBottom: 2,
  },
  donorEmail: {
    fontSize: 14,
    color: "#666",
    marginBottom: 2,
  },
  donorPhone: {
    fontSize: 12,
    color: "#888",
  },
  pendingBadge: {
    flexDirection: "row",
    alignItems: "center",
    paddingHorizontal: 8,
    paddingVertical: 4,
    borderRadius: 12,
    backgroundColor: "#FFF3E0",
    gap: 4,
  },
  pendingText: {
    fontSize: 12,
    fontWeight: "600",
    color: "#FF9800",
  },
  cardBody: {
    gap: 8,
    marginBottom: 16,
  },
  articleSection: {
    marginBottom: 8,
  },
  articleName: {
    fontSize: 16,
    fontWeight: "bold",
    color: "#8B4513",
    marginBottom: 4,
  },
  articleDetails: {
    fontSize: 14,
    color: "#666",
    marginBottom: 4,
  },
  sourceWarning: {
    fontSize: 12,
    color: "#FF9800",
    fontStyle: "italic",
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
