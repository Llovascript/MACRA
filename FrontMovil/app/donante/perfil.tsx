"use client"

import { useState, useEffect } from "react"
import { View, Text, StyleSheet, ScrollView, SafeAreaView, TouchableOpacity, Alert } from "react-native"
import { MaterialCommunityIcons } from "@expo/vector-icons"
import { useRouter } from "expo-router"
import AsyncStorage from "@react-native-async-storage/async-storage"
import { generateFastApiUrl } from "../../utils"

interface Usuario {
  id: number
  nombre: string
  aP: string
  aM: string
  edad?: number
  telefono: string
  correo: string
  rfc: string
  paginaWeb?: string
  fundacion?: string
  aprobacion: boolean
  rol: {
    id: number
    nombre: string
  }
}

export default function DonantePerfilScreen() {
  const [usuario, setUsuario] = useState<Usuario | null>(null)
  const [loading, setLoading] = useState(true)
  const router = useRouter()

  useEffect(() => {
    fetchUserProfile()
  }, [])

  const fetchUserProfile = async () => {
    try {
      const token = await AsyncStorage.getItem("token")

      if (!token) {
        Alert.alert("Error", "No se encontró información de sesión")
        router.replace("/login")
        return
      }

      // Usar el endpoint /auth/me que ya existe y funciona
      const response = await fetch(generateFastApiUrl("/auth/me"), {
        headers: {
          Authorization: `Bearer ${token}`,
          "Content-Type": "application/json",
        },
      })

      if (response.ok) {
        const userData = await response.json()
        console.log("User data received:", userData)
        setUsuario(userData)
      } else if (response.status === 401) {
        Alert.alert("Sesión Expirada", "Tu sesión ha expirado. Por favor inicia sesión nuevamente.")
        await AsyncStorage.multiRemove(["access_token", "user_role", "user_name", "user_id"])
        router.replace("/login")
      } else {
        Alert.alert("Error", "No se pudo cargar la información del perfil")
      }
    } catch (error) {
      console.error("Error fetching user profile:", error)
      Alert.alert("Error", "Error de conexión. Verifica tu conexión a internet.")
    } finally {
      setLoading(false)
    }
  }

  const handleGoBack = () => {
    router.back()
  }

  if (loading) {
    return (
      <SafeAreaView style={styles.container}>
        <View style={styles.loadingContainer}>
          <MaterialCommunityIcons name="loading" size={40} color="#2196F3" />
          <Text style={styles.loadingText}>Cargando perfil...</Text>
        </View>
      </SafeAreaView>
    )
  }

  if (!usuario) {
    return (
      <SafeAreaView style={styles.container}>
        <View style={styles.errorContainer}>
          <MaterialCommunityIcons name="alert-circle" size={60} color="#f44336" />
          <Text style={styles.errorText}>No se pudo cargar la información del perfil</Text>
          <TouchableOpacity style={styles.retryButton} onPress={fetchUserProfile}>
            <Text style={styles.retryButtonText}>Reintentar</Text>
          </TouchableOpacity>
        </View>
      </SafeAreaView>
    )
  }

  return (
    <SafeAreaView style={styles.container}>
      <View style={styles.header}>
        <TouchableOpacity style={styles.backButton} onPress={handleGoBack}>
          <MaterialCommunityIcons name="arrow-left" size={24} color="#fff" />
        </TouchableOpacity>
        <Text style={styles.headerTitle}>Mi Perfil</Text>
        <View style={styles.placeholder} />
      </View>

      <ScrollView style={styles.content}>
        <View style={styles.profileCard}>
          <View style={styles.avatarContainer}>
            <MaterialCommunityIcons name="account-circle" size={80} color="#2196F3" />
          </View>

          <Text style={styles.userName}>
            {usuario.nombre} {usuario.aP} {usuario.aM}
          </Text>

          <View style={styles.statusContainer}>
            <MaterialCommunityIcons
              name={usuario.aprobacion ? "check-circle" : "clock"}
              size={20}
              color={usuario.aprobacion ? "#4CAF50" : "#FF9800"}
            />
            <Text style={[styles.statusText, { color: usuario.aprobacion ? "#4CAF50" : "#FF9800" }]}>
              {usuario.aprobacion ? "Cuenta Aprobada" : "Pendiente de Aprobación"}
            </Text>
          </View>
        </View>

        <View style={styles.infoSection}>
          <Text style={styles.sectionTitle}>Información Personal</Text>

          <View style={styles.infoCard}>
            <View style={styles.infoRow}>
              <MaterialCommunityIcons name="email" size={20} color="#666" />
              <View style={styles.infoContent}>
                <Text style={styles.infoLabel}>Correo Electrónico</Text>
                <Text style={styles.infoValue}>{usuario.correo}</Text>
              </View>
            </View>

            <View style={styles.infoRow}>
              <MaterialCommunityIcons name="phone" size={20} color="#666" />
              <View style={styles.infoContent}>
                <Text style={styles.infoLabel}>Teléfono</Text>
                <Text style={styles.infoValue}>{usuario.telefono}</Text>
              </View>
            </View>

            <View style={styles.infoRow}>
              <MaterialCommunityIcons name="card-account-details" size={20} color="#666" />
              <View style={styles.infoContent}>
                <Text style={styles.infoLabel}>RFC</Text>
                <Text style={styles.infoValue}>{usuario.rfc}</Text>
              </View>
            </View>

            {usuario.edad && (
              <View style={styles.infoRow}>
                <MaterialCommunityIcons name="calendar" size={20} color="#666" />
                <View style={styles.infoContent}>
                  <Text style={styles.infoLabel}>Edad</Text>
                  <Text style={styles.infoValue}>{usuario.edad} años</Text>
                </View>
              </View>
            )}

            {usuario.paginaWeb && (
              <View style={styles.infoRow}>
                <MaterialCommunityIcons name="web" size={20} color="#666" />
                <View style={styles.infoContent}>
                  <Text style={styles.infoLabel}>Página Web</Text>
                  <Text style={styles.infoValue}>{usuario.paginaWeb}</Text>
                </View>
              </View>
            )}

            {usuario.fundacion && (
              <View style={styles.infoRow}>
                <MaterialCommunityIcons name="calendar-star" size={20} color="#666" />
                <View style={styles.infoContent}>
                  <Text style={styles.infoLabel}>Fecha de Fundación</Text>
                  <Text style={styles.infoValue}>{new Date(usuario.fundacion).toLocaleDateString()}</Text>
                </View>
              </View>
            )}
          </View>
        </View>

        <View style={styles.roleSection}>
          <Text style={styles.sectionTitle}>Información de Cuenta</Text>

          <View style={styles.infoCard}>
            <View style={styles.infoRow}>
              <MaterialCommunityIcons name="account-group" size={20} color="#666" />
              <View style={styles.infoContent}>
                <Text style={styles.infoLabel}>Rol</Text>
                <Text style={styles.infoValue}>{usuario.rol.nombre}</Text>
              </View>
            </View>
          </View>
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
  loadingContainer: {
    flex: 1,
    justifyContent: "center",
    alignItems: "center",
  },
  loadingText: {
    fontSize: 16,
    color: "#666",
    marginTop: 10,
  },
  errorContainer: {
    flex: 1,
    justifyContent: "center",
    alignItems: "center",
    padding: 20,
  },
  errorText: {
    fontSize: 16,
    color: "#666",
    textAlign: "center",
    marginTop: 10,
    marginBottom: 20,
  },
  retryButton: {
    backgroundColor: "#2196F3",
    paddingHorizontal: 20,
    paddingVertical: 10,
    borderRadius: 8,
  },
  retryButtonText: {
    color: "#fff",
    fontSize: 16,
    fontWeight: "600",
  },
  header: {
    flexDirection: "row",
    alignItems: "center",
    justifyContent: "space-between",
    backgroundColor: "#2196F3",
    paddingHorizontal: 20,
    paddingVertical: 15,
    paddingTop: 50,
  },
  backButton: {
    padding: 5,
  },
  headerTitle: {
    fontSize: 20,
    fontWeight: "bold",
    color: "#fff",
  },
  placeholder: {
    width: 34,
  },
  content: {
    flex: 1,
    padding: 20,
  },
  profileCard: {
    backgroundColor: "#fff",
    borderRadius: 15,
    padding: 20,
    alignItems: "center",
    marginBottom: 20,
    shadowColor: "#000",
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 3,
  },
  avatarContainer: {
    marginBottom: 15,
  },
  userName: {
    fontSize: 24,
    fontWeight: "bold",
    color: "#333",
    textAlign: "center",
    marginBottom: 10,
  },
  statusContainer: {
    flexDirection: "row",
    alignItems: "center",
  },
  statusText: {
    fontSize: 14,
    fontWeight: "600",
    marginLeft: 5,
  },
  infoSection: {
    marginBottom: 20,
  },
  roleSection: {
    marginBottom: 20,
  },
  sectionTitle: {
    fontSize: 18,
    fontWeight: "bold",
    color: "#333",
    marginBottom: 10,
  },
  infoCard: {
    backgroundColor: "#fff",
    borderRadius: 10,
    padding: 15,
    shadowColor: "#000",
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 3,
  },
  infoRow: {
    flexDirection: "row",
    alignItems: "center",
    paddingVertical: 12,
    borderBottomWidth: 1,
    borderBottomColor: "#f0f0f0",
  },
  infoContent: {
    flex: 1,
    marginLeft: 15,
  },
  infoLabel: {
    fontSize: 12,
    color: "#666",
    marginBottom: 2,
  },
  infoValue: {
    fontSize: 16,
    color: "#333",
    fontWeight: "500",
  },
})
