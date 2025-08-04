"use client"

import { useState, useEffect } from "react"
import {
  View,
  Text,
  StyleSheet,
  FlatList,
  TouchableOpacity,
  Alert,
  RefreshControl,
  ActivityIndicator,
} from "react-native"
import AsyncStorage from "@react-native-async-storage/async-storage"
import { generateFastApiUrl } from "../../utils"
import { router } from "expo-router"

interface Evento {
  id: number
  nombre: string
  fechaIn: string
  fechaTer: string
  descripcion: string
  del_flag: boolean
  yaRegistrado?: boolean
}

export default function EventosScreen() {
  const [eventos, setEventos] = useState<Evento[]>([])
  const [loading, setLoading] = useState(true)
  const [refreshing, setRefreshing] = useState(false)
  const [joiningEvent, setJoiningEvent] = useState<number | null>(null)
  const [userId, setUserId] = useState<number | null>(null)

  const loadEventos = async () => {
    try {
      const token = await AsyncStorage.getItem("token")
      if (!token) {
        Alert.alert("Error", "No se encontró token de autenticación")
        router.replace("/login")
        return
      }

      // Obtener información del usuario para conseguir el ID
      const userResponse = await fetch(generateFastApiUrl("/me"), {
        headers: {
          Authorization: `Bearer ${token}`,
          "Content-Type": "application/json",
        },
      })

      let currentUserId = null
      if (userResponse.ok) {
        const userData = await userResponse.json()
        currentUserId = userData.id
        setUserId(currentUserId)
      }

      // Cargar eventos disponibles
      const eventosResponse = await fetch(generateFastApiUrl("/eventos/"), {
        headers: {
          Authorization: `Bearer ${token}`,
          "Content-Type": "application/json",
        },
      })

      if (eventosResponse.ok) {
        const eventosData = await eventosResponse.json()

        // Filtrar solo eventos activos (del_flag = 0 o false)
        const eventosActivos = eventosData.filter((evento: Evento) => !evento.del_flag)

        // Si tenemos el ID del usuario, verificar cuáles eventos ya están registrados
        if (currentUserId) {
          const eventosConEstado = await Promise.all(
            eventosActivos.map(async (evento: Evento) => {
              // Aquí podrías hacer una consulta directa a la base de datos si fuera necesario
              // Por ahora, asumimos que no está registrado hasta que se una
              return {
                ...evento,
                yaRegistrado: false,
              }
            }),
          )
          setEventos(eventosConEstado)
        } else {
          setEventos(eventosActivos.map((evento: Evento) => ({ ...evento, yaRegistrado: false })))
        }
      } else {
        Alert.alert("Error", "No se pudieron cargar los eventos")
      }
    } catch (error) {
      console.error("Error cargando eventos:", error)
      Alert.alert("Error", "Error de conexión")
    } finally {
      setLoading(false)
      setRefreshing(false)
    }
  }

  const unirseAlEvento = async (eventoId: number) => {
    try {
      setJoiningEvent(eventoId)
      const token = await AsyncStorage.getItem("token")

      const response = await fetch(generateFastApiUrl(`/eventos/${eventoId}/unirse_como_donante`), {
        method: "POST",
        headers: {
          Authorization: `Bearer ${token}`,
          "Content-Type": "application/json",
        },
      })

      if (response.ok) {
        Alert.alert("¡Éxito!", "Te has unido al evento correctamente")
        // Actualizar el estado local
        setEventos((prev) =>
          prev.map((evento) => (evento.id === eventoId ? { ...evento, yaRegistrado: true } : evento)),
        )
      } else {
        const errorData = await response.json()
        Alert.alert("Error", errorData.detail || "No se pudo unir al evento")
      }
    } catch (error) {
      console.error("Error uniéndose al evento:", error)
      Alert.alert("Error", "Error de conexión")
    } finally {
      setJoiningEvent(null)
    }
  }

  const formatDate = (dateString: string) => {
    const date = new Date(dateString)
    return date.toLocaleDateString("es-ES", {
      day: "2-digit",
      month: "2-digit",
      year: "numeric",
    })
  }

  const renderEvento = ({ item }: { item: Evento }) => (
    <View style={styles.eventoCard}>
      <View style={styles.eventoHeader}>
        <Text style={styles.eventoNombre}>{item.nombre}</Text>
        {item.yaRegistrado && (
          <View style={styles.registradoBadge}>
            <Text style={styles.registradoText}>Registrado</Text>
          </View>
        )}
      </View>

      <Text style={styles.eventoDescripcion}>{item.descripcion}</Text>

      <View style={styles.fechasContainer}>
        <View style={styles.fechaItem}>
          <Text style={styles.fechaLabel}>Inicio:</Text>
          <Text style={styles.fechaValue}>{formatDate(item.fechaIn)}</Text>
        </View>
        <View style={styles.fechaItem}>
          <Text style={styles.fechaLabel}>Fin:</Text>
          <Text style={styles.fechaValue}>{formatDate(item.fechaTer)}</Text>
        </View>
      </View>

      <View style={styles.buttonContainer}>
        {item.yaRegistrado ? (
          <View style={styles.yaRegistradoContainer}>
            <Text style={styles.yaRegistradoText}>✓ Ya estás registrado en este evento</Text>
          </View>
        ) : (
          <TouchableOpacity
            style={[styles.unirseButton, joiningEvent === item.id && styles.buttonDisabled]}
            onPress={() => unirseAlEvento(item.id)}
            disabled={joiningEvent === item.id}
          >
            {joiningEvent === item.id ? (
              <ActivityIndicator color="#fff" size="small" />
            ) : (
              <Text style={styles.unirseButtonText}>Unirse al Evento</Text>
            )}
          </TouchableOpacity>
        )}
      </View>
    </View>
  )

  useEffect(() => {
    loadEventos()
  }, [])

  const onRefresh = () => {
    setRefreshing(true)
    loadEventos()
  }

  if (loading) {
    return (
      <View style={styles.loadingContainer}>
        <ActivityIndicator size="large" color="#007AFF" />
        <Text style={styles.loadingText}>Cargando eventos...</Text>
      </View>
    )
  }

  const eventosRegistrados = eventos.filter((e) => e.yaRegistrado).length

  return (
    <View style={styles.container}>
      <View style={styles.header}>
        <Text style={styles.title}>Eventos Disponibles</Text>
        <Text style={styles.subtitle}>
          {eventos.length} eventos • {eventosRegistrados} registrado{eventosRegistrados !== 1 ? "s" : ""}
        </Text>
      </View>

      <FlatList
        data={eventos}
        renderItem={renderEvento}
        keyExtractor={(item) => item.id.toString()}
        contentContainerStyle={styles.listContainer}
        refreshControl={<RefreshControl refreshing={refreshing} onRefresh={onRefresh} />}
        ListEmptyComponent={
          <View style={styles.emptyContainer}>
            <Text style={styles.emptyText}>No hay eventos disponibles</Text>
            <Text style={styles.emptySubtext}>Los eventos aparecerán aquí cuando estén disponibles</Text>
          </View>
        }
      />
    </View>
  )
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: "#f5f5f5",
  },
  header: {
    backgroundColor: "#fff",
    padding: 20,
    borderBottomWidth: 1,
    borderBottomColor: "#e0e0e0",
  },
  title: {
    fontSize: 24,
    fontWeight: "bold",
    color: "#333",
    marginBottom: 4,
  },
  subtitle: {
    fontSize: 14,
    color: "#666",
  },
  listContainer: {
    padding: 16,
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
  eventoHeader: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "flex-start",
    marginBottom: 8,
  },
  eventoNombre: {
    fontSize: 18,
    fontWeight: "bold",
    color: "#333",
    flex: 1,
    marginRight: 8,
  },
  registradoBadge: {
    backgroundColor: "#4CAF50",
    paddingHorizontal: 8,
    paddingVertical: 4,
    borderRadius: 12,
  },
  registradoText: {
    color: "#fff",
    fontSize: 12,
    fontWeight: "600",
  },
  eventoDescripcion: {
    fontSize: 14,
    color: "#666",
    marginBottom: 12,
    lineHeight: 20,
  },
  fechasContainer: {
    flexDirection: "row",
    justifyContent: "space-between",
    marginBottom: 16,
  },
  fechaItem: {
    flex: 1,
  },
  fechaLabel: {
    fontSize: 12,
    color: "#999",
    marginBottom: 2,
  },
  fechaValue: {
    fontSize: 14,
    fontWeight: "600",
    color: "#333",
  },
  buttonContainer: {
    marginTop: 8,
  },
  unirseButton: {
    backgroundColor: "#007AFF",
    paddingVertical: 12,
    paddingHorizontal: 24,
    borderRadius: 8,
    alignItems: "center",
  },
  unirseButtonText: {
    color: "#fff",
    fontSize: 16,
    fontWeight: "600",
  },
  yaRegistradoContainer: {
    backgroundColor: "#f0f8f0",
    paddingVertical: 12,
    paddingHorizontal: 24,
    borderRadius: 8,
    alignItems: "center",
    borderWidth: 1,
    borderColor: "#4CAF50",
  },
  yaRegistradoText: {
    color: "#4CAF50",
    fontSize: 16,
    fontWeight: "600",
  },
  buttonDisabled: {
    opacity: 0.6,
  },
  loadingContainer: {
    flex: 1,
    justifyContent: "center",
    alignItems: "center",
    backgroundColor: "#f5f5f5",
  },
  loadingText: {
    marginTop: 12,
    fontSize: 16,
    color: "#666",
  },
  emptyContainer: {
    alignItems: "center",
    paddingVertical: 40,
  },
  emptyText: {
    fontSize: 18,
    fontWeight: "600",
    color: "#333",
    marginBottom: 8,
  },
  emptySubtext: {
    fontSize: 14,
    color: "#666",
    textAlign: "center",
  },
})
