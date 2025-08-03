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

interface Donacion {
  id: number
  tipo_donante: string | null
  fecha: string
  cantidad: number
  aprobacion: boolean | null
  articuloP_id: number
  estatus_id: number
  usuario_id: number
}

export default function DonacionesScreen() {
  const [donaciones, setDonaciones] = useState<Donacion[]>([])
  const [loading, setLoading] = useState(true)
  const [refreshing, setRefreshing] = useState(false)
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
    loadDonaciones()
  }, [])

  const loadDonaciones = async () => {
    try {
      const token = await AsyncStorage.getItem("token")
      if (!token) {
        Alert.alert("Error", "No se encontró información de autenticación")
        router.replace("/login")
        return
      }

      const response = await fetch(generateFastApiUrl("/donaciones/me/"), {
        headers: {
          Authorization: `Bearer ${token}`,
          "Content-Type": "application/json",
        },
      })

      if (response.ok) {
        const data = await response.json()
        setDonaciones(data || [])
      } else {
        console.error("Error al cargar donaciones:", response.status)
        Alert.alert("Error", "No se pudieron cargar las donaciones")
      }
    } catch (error) {
      console.error("Error loading donaciones:", error)
      Alert.alert("Error", "Error de conexión al cargar las donaciones")
    } finally {
      setLoading(false)
      setRefreshing(false)
    }
  }

  const onRefresh = () => {
    setRefreshing(true)
    loadDonaciones()
  }

  const getArticuloInfo = (articuloPId: number) => {
    const backup = presentacionesBackup[articuloPId]
    if (backup) {
      return {
        nombre: backup.nombre,
        unidad: backup.unidad,
        categoria: backup.categoria,
        source: "backup",
      }
    }

    return {
      nombre: `Artículo ID ${articuloPId}`,
      unidad: "Unidad",
      categoria: "Sin categoría",
      source: "generic",
    }
  }

  const getStatusInfo = (donacion: Donacion) => {
    // Verificar primero si aprobacion es null (pendiente)
    if (donacion.aprobacion === null) {
      return {
        text: "Pendiente",
        color: "#FF9800",
        icon: "clock" as const,
        bgColor: "#FFF3E0",
      }
    } else if (donacion.aprobacion === true) {
      return {
        text: "Aprobada",
        color: "#4CAF50",
        icon: "check-circle" as const,
        bgColor: "#E8F5E8",
      }
    } else {
      return {
        text: "Rechazada",
        color: "#F44336",
        icon: "close-circle" as const,
        bgColor: "#FFEBEE",
      }
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

  const renderDonacionItem = (donacion: Donacion) => {
    const articuloInfo = getArticuloInfo(donacion.articuloP_id)
    const statusInfo = getStatusInfo(donacion)

    return (
      <View key={donacion.id} style={styles.donacionCard}>
        <View style={styles.cardHeader}>
          <View style={styles.articleInfo}>
            <Text style={styles.articleName}>{articuloInfo.nombre}</Text>
            <Text style={styles.articleDetails}>
              {donacion.cantidad} {articuloInfo.unidad} • {articuloInfo.categoria}
            </Text>

          </View>
          <View style={[styles.statusBadge, { backgroundColor: statusInfo.bgColor }]}>
            <MaterialCommunityIcons name={statusInfo.icon} size={16} color={statusInfo.color} />
            <Text style={[styles.statusText, { color: statusInfo.color }]}>{statusInfo.text}</Text>
          </View>
        </View>

        <View style={styles.cardBody}>
          <View style={styles.infoRow}>
            <MaterialCommunityIcons name="calendar" size={16} color="#666" />
            <Text style={styles.infoText}>Fecha: {formatDate(donacion.fecha)}</Text>
          </View>

          <View style={styles.infoRow}>
            <MaterialCommunityIcons name="package-variant" size={16} color="#666" />
            <Text style={styles.infoText}>Cantidad: {donacion.cantidad} presentaciones</Text>
          </View>
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
          <Text style={styles.headerTitle}>Mis Donaciones</Text>
        </View>
        <View style={styles.loadingContainer}>
          <ActivityIndicator size="large" color="#8B4513" />
          <Text style={styles.loadingText}>Cargando donaciones...</Text>
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
        <Text style={styles.headerTitle}>Mis Donaciones</Text>
        <TouchableOpacity style={styles.addButton} onPress={() => router.push("/donante/nueva-donacion")}>
          <MaterialCommunityIcons name="plus" size={24} color="#fff" />
        </TouchableOpacity>
      </View>

      <ScrollView
        style={styles.content}
        showsVerticalScrollIndicator={false}
        refreshControl={<RefreshControl refreshing={refreshing} onRefresh={onRefresh} colors={["#8B4513"]} />}
      >
        <View style={styles.statsContainer}>
          <View style={styles.statCard}>
            <Text style={styles.statNumber}>{donaciones.length}</Text>
            <Text style={styles.statLabel}>Total Donaciones</Text>
          </View>
          <View style={styles.statCard}>
            <Text style={styles.statNumber}>{donaciones.filter((d) => d.aprobacion === true).length}</Text>
            <Text style={styles.statLabel}>Aprobadas</Text>
          </View>
          <View style={styles.statCard}>
            <Text style={styles.statNumber}>{donaciones.filter((d) => d.aprobacion === null).length}</Text>
            <Text style={styles.statLabel}>Pendientes</Text>
          </View>
        </View>

        <View style={styles.section}>
          <Text style={styles.sectionTitle}>Historial de Donaciones</Text>

          {donaciones.length === 0 ? (
            <View style={styles.emptyContainer}>
              <MaterialCommunityIcons name="gift-outline" size={64} color="#ccc" />
              <Text style={styles.emptyTitle}>No tienes donaciones</Text>
              <Text style={styles.emptySubtitle}>Crea tu primera solicitud de donación</Text>
              <TouchableOpacity style={styles.createButton} onPress={() => router.push("/donante/nueva-donacion")}>
                <MaterialCommunityIcons name="plus" size={20} color="#fff" />
                <Text style={styles.createButtonText}>Crear Donación</Text>
              </TouchableOpacity>
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
    paddingTop: 20,
    paddingBottom: 15,
    paddingHorizontal: 20,
    flexDirection: "row",
    alignItems: "center",
    justifyContent: "space-between",
  },
  backButton: {
    marginRight: 15,
  },
  headerTitle: {
    fontSize: 20,
    fontWeight: "bold",
    color: "#fff",
    flex: 1,
  },
  addButton: {
    padding: 5,
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
  articleInfo: {
    flex: 1,
    marginRight: 10,
  },
  articleName: {
    fontSize: 16,
    fontWeight: "bold",
    color: "#333",
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
  statusBadge: {
    flexDirection: "row",
    alignItems: "center",
    paddingHorizontal: 8,
    paddingVertical: 4,
    borderRadius: 12,
    gap: 4,
  },
  statusText: {
    fontSize: 12,
    fontWeight: "600",
  },
  cardBody: {
    gap: 8,
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
    marginBottom: 24,
  },
  createButton: {
    backgroundColor: "#8B4513",
    borderRadius: 8,
    paddingHorizontal: 20,
    paddingVertical: 12,
    flexDirection: "row",
    alignItems: "center",
    gap: 8,
  },
  createButtonText: {
    color: "#fff",
    fontSize: 16,
    fontWeight: "600",
  },
})
