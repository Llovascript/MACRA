"use client"

import { useState } from "react"
import {
  View,
  Text,
  StyleSheet,
  TextInput,
  TouchableOpacity,
  Alert,
  SafeAreaView,
  ScrollView,
  ActivityIndicator,
  Modal,
  FlatList,
} from "react-native"
import { useRouter } from "expo-router"
import { MaterialCommunityIcons } from "@expo/vector-icons"
import AsyncStorage from "@react-native-async-storage/async-storage"
import { generateFastApiUrl } from "@/utils"

interface PresentacionManual {
  id: number
  nombre: string
  cantidad: number
  unidad: string
  categoria: string
}

export default function NuevaDonacionScreen() {
  const [articuloPId, setArticuloPId] = useState<number | null>(null)
  const [cantidadDonacion, setCantidadDonacion] = useState("")
  const [loading, setLoading] = useState(false)
  const [showPresentacionModal, setShowPresentacionModal] = useState(false)
  const [selectedPresentacion, setSelectedPresentacion] = useState<PresentacionManual | null>(null)
  const router = useRouter()

  // Presentaciones manuales - puedes agregar o modificar estas opciones
  const presentacionesDisponibles: PresentacionManual[] = [
    {
      id: 1,
      nombre: "Arroz",
      cantidad: 1,
      unidad: "Kilogramo",
      categoria: "Alimentos",
    },
    {
      id: 2,
      nombre: "Frijoles",
      cantidad: 1,
      unidad: "Kilogramo",
      categoria: "Alimentos",
    },
    {
      id: 3,
      nombre: "Aceite",
      cantidad: 1,
      unidad: "Litro",
      categoria: "Alimentos",
    },
    {
      id: 4,
      nombre: "Leche",
      cantidad: 1,
      unidad: "Litro",
      categoria: "Alimentos",
    },
    {
      id: 5,
      nombre: "Huevos",
      cantidad: 12,
      unidad: "Piezas",
      categoria: "Alimentos",
    },
    {
      id: 6,
      nombre: "Pan",
      cantidad: 1,
      unidad: "Pieza",
      categoria: "Alimentos",
    },
    {
      id: 7,
      nombre: "Azúcar",
      cantidad: 1,
      unidad: "Kilogramo",
      categoria: "Alimentos",
    },
    {
      id: 8,
      nombre: "Sal",
      cantidad: 1,
      unidad: "Kilogramo",
      categoria: "Alimentos",
    },
    {
      id: 9,
      nombre: "Pasta",
      cantidad: 500,
      unidad: "Gramos",
      categoria: "Alimentos",
    },
    {
      id: 10,
      nombre: "Atún",
      cantidad: 1,
      unidad: "Lata",
      categoria: "Alimentos",
    },
    {
      id: 11,
      nombre: "Jabón",
      cantidad: 1,
      unidad: "Pieza",
      categoria: "Limpieza",
    },
    {
      id: 12,
      nombre: "Detergente",
      cantidad: 1,
      unidad: "Litro",
      categoria: "Limpieza",
    },
    {
      id: 13,
      nombre: "Papel Higiénico",
      cantidad: 4,
      unidad: "Rollos",
      categoria: "Higiene",
    },
    {
      id: 14,
      nombre: "Pasta de Dientes",
      cantidad: 1,
      unidad: "Tubo",
      categoria: "Higiene",
    },
    {
      id: 15,
      nombre: "Shampoo",
      cantidad: 1,
      unidad: "Botella",
      categoria: "Higiene",
    },
  ]

  const validateForm = () => {
    if (!articuloPId) {
      Alert.alert("Error", "Por favor selecciona un artículo")
      return false
    }
    if (!cantidadDonacion.trim()) {
      Alert.alert("Error", "Por favor ingresa la cantidad a donar")
      return false
    }
    const cantDonNum = Number.parseInt(cantidadDonacion)
    if (isNaN(cantDonNum) || cantDonNum <= 0) {
      Alert.alert("Error", "La cantidad a donar debe ser un número mayor a 0")
      return false
    }
    return true
  }

  const handleSubmit = async () => {
    if (!validateForm()) return

    setLoading(true)
    try {
      const token = await AsyncStorage.getItem("token")
      const userDataString = await AsyncStorage.getItem("user")

      if (!token || !userDataString) {
        Alert.alert("Error", "No se encontró información de autenticación")
        router.replace("/login")
        return
      }

      const userData = JSON.parse(userDataString)

      // Crear la donación - NO incluir aprobacion en el objeto para que el backend use su valor por defecto
      const donacionData = {
        tipo_donante: "persona",
        fecha: new Date().toISOString().split("T")[0],
        cantidad: Number.parseInt(cantidadDonacion),
        usuario_id: userData.id,
        articuloP_id: articuloPId,
        estatus_id: 3, // ID 3 = Pendiente
        // NO incluir aprobacion para que el backend use null por defecto
      }

      console.log("Creating donacion:", donacionData)

      const response = await fetch(generateFastApiUrl("/donaciones/"), {
        method: "POST",
        headers: {
          Authorization: `Bearer ${token}`,
          "Content-Type": "application/json",
        },
        body: JSON.stringify(donacionData),
      })

      if (!response.ok) {
        const errorData = await response.text()
        console.error("Error creating donacion:", errorData)
        throw new Error("No se pudo crear la donación")
      }

      const donacionCreada = await response.json()
      console.log("Donacion created:", donacionCreada)

      Alert.alert("¡Éxito!", "Tu solicitud de donación ha sido creada correctamente y está pendiente de aprobación.", [
        {
          text: "OK",
          onPress: () => router.back(),
        },
      ])
    } catch (error) {
      console.error("Error in submission:", error)
      Alert.alert("Error", error instanceof Error ? error.message : "Ocurrió un error al crear la donación")
    } finally {
      setLoading(false)
    }
  }

  const renderPresentacionItem = ({ item }: { item: PresentacionManual }) => (
    <TouchableOpacity
      style={styles.modalItem}
      onPress={() => {
        setSelectedPresentacion(item)
        setArticuloPId(item.id)
        setShowPresentacionModal(false)
      }}
    >
      <View style={styles.presentacionItem}>
        <Text style={styles.presentacionTitle}>{item.nombre}</Text>
        <Text style={styles.presentacionSubtitle}>
          {item.cantidad} {item.unidad} • {item.categoria}
        </Text>
      </View>
    </TouchableOpacity>
  )

  return (
    <SafeAreaView style={styles.container}>
      <View style={styles.header}>
        <TouchableOpacity style={styles.backButton} onPress={() => router.back()}>
          <MaterialCommunityIcons name="arrow-left" size={24} color="#fff" />
        </TouchableOpacity>
        <Text style={styles.headerTitle}>Nueva Donación</Text>
      </View>

      <ScrollView style={styles.content} showsVerticalScrollIndicator={false}>
        <View style={styles.form}>
          <Text style={styles.sectionTitle}>Crear Solicitud de Donación</Text>
          <Text style={styles.sectionSubtitle}>
            Selecciona el artículo que deseas donar de los disponibles en nuestro sistema
          </Text>

          <View style={styles.inputGroup}>
            <Text style={styles.label}>Artículo a Donar * ({presentacionesDisponibles.length} disponibles)</Text>
            <TouchableOpacity style={styles.selector} onPress={() => setShowPresentacionModal(true)}>
              <View style={styles.selectorContent}>
                {selectedPresentacion ? (
                  <View>
                    <Text style={styles.selectorText}>{selectedPresentacion.nombre}</Text>
                    <Text style={styles.selectorSubtext}>
                      {selectedPresentacion.cantidad} {selectedPresentacion.unidad} • {selectedPresentacion.categoria}
                    </Text>
                  </View>
                ) : (
                  <Text style={styles.placeholder}>Selecciona un artículo</Text>
                )}
              </View>
              <MaterialCommunityIcons name="chevron-down" size={24} color="#666" />
            </TouchableOpacity>
          </View>

          <View style={styles.inputGroup}>
            <Text style={styles.label}>Cantidad a Donar *</Text>
            <TextInput
              style={styles.input}
              value={cantidadDonacion}
              onChangeText={setCantidadDonacion}
              placeholder="Ej: 10, 25, 50"
              placeholderTextColor="#999"
              keyboardType="numeric"
            />
            <Text style={styles.helpText}>
              {selectedPresentacion
                ? `Número de presentaciones de ${selectedPresentacion.cantidad} ${selectedPresentacion.unidad} que deseas donar`
                : "Número de presentaciones que deseas donar"}
            </Text>
          </View>

          <View style={styles.infoContainer}>
            <MaterialCommunityIcons name="information" size={20} color="#2196F3" />
            <Text style={styles.infoText}>
              Tu solicitud de donación quedará pendiente de aprobación por parte del administrador. La fecha se
              registrará automáticamente como hoy.
            </Text>
          </View>

          <TouchableOpacity
            style={[styles.submitButton, loading && styles.submitButtonDisabled]}
            onPress={handleSubmit}
            disabled={loading}
          >
            {loading ? (
              <ActivityIndicator size="small" color="#fff" />
            ) : (
              <>
                <MaterialCommunityIcons name="send" size={20} color="#fff" />
                <Text style={styles.submitButtonText}>Crear Solicitud de Donación</Text>
              </>
            )}
          </TouchableOpacity>
        </View>
      </ScrollView>

      {/* Modal para seleccionar presentación */}
      <Modal
        visible={showPresentacionModal}
        transparent
        animationType="slide"
        onRequestClose={() => setShowPresentacionModal(false)}
      >
        <View style={styles.modalOverlay}>
          <View style={styles.modalContent}>
            <View style={styles.modalHeader}>
              <Text style={styles.modalTitle}>Seleccionar Artículo ({presentacionesDisponibles.length})</Text>
              <TouchableOpacity onPress={() => setShowPresentacionModal(false)}>
                <MaterialCommunityIcons name="close" size={24} color="#666" />
              </TouchableOpacity>
            </View>
            <FlatList
              data={presentacionesDisponibles}
              renderItem={renderPresentacionItem}
              keyExtractor={(item) => item.id.toString()}
              style={styles.modalList}
            />
          </View>
        </View>
      </Modal>
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
  },
  backButton: {
    marginRight: 15,
  },
  headerTitle: {
    fontSize: 20,
    fontWeight: "bold",
    color: "#fff",
  },
  content: {
    flex: 1,
  },
  form: {
    padding: 20,
  },
  sectionTitle: {
    fontSize: 20,
    fontWeight: "bold",
    color: "#333",
    marginBottom: 5,
  },
  sectionSubtitle: {
    fontSize: 14,
    color: "#666",
    marginBottom: 20,
    lineHeight: 20,
  },
  inputGroup: {
    marginBottom: 20,
  },
  label: {
    fontSize: 16,
    fontWeight: "600",
    color: "#333",
    marginBottom: 8,
  },
  input: {
    backgroundColor: "#fff",
    borderRadius: 8,
    padding: 15,
    fontSize: 16,
    borderWidth: 1,
    borderColor: "#ddd",
  },
  selector: {
    backgroundColor: "#fff",
    borderRadius: 8,
    padding: 15,
    borderWidth: 1,
    borderColor: "#ddd",
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "center",
  },
  selectorContent: {
    flex: 1,
  },
  selectorText: {
    fontSize: 16,
    color: "#333",
    fontWeight: "600",
  },
  selectorSubtext: {
    fontSize: 14,
    color: "#666",
    marginTop: 2,
  },
  placeholder: {
    fontSize: 16,
    color: "#999",
  },
  helpText: {
    fontSize: 12,
    color: "#666",
    marginTop: 5,
    fontStyle: "italic",
  },
  infoContainer: {
    backgroundColor: "#E3F2FD",
    borderRadius: 8,
    padding: 15,
    flexDirection: "row",
    alignItems: "flex-start",
    marginBottom: 20,
    borderLeftWidth: 4,
    borderLeftColor: "#2196F3",
  },
  infoText: {
    flex: 1,
    marginLeft: 10,
    fontSize: 14,
    color: "#1565C0",
    lineHeight: 18,
  },
  submitButton: {
    backgroundColor: "#8B4513",
    borderRadius: 8,
    padding: 15,
    flexDirection: "row",
    justifyContent: "center",
    alignItems: "center",
    marginTop: 10,
  },
  submitButtonDisabled: {
    backgroundColor: "#ccc",
  },
  submitButtonText: {
    color: "#fff",
    fontSize: 16,
    fontWeight: "600",
    marginLeft: 8,
  },
  modalOverlay: {
    flex: 1,
    backgroundColor: "rgba(0, 0, 0, 0.5)",
    justifyContent: "flex-end",
  },
  modalContent: {
    backgroundColor: "#fff",
    borderTopLeftRadius: 20,
    borderTopRightRadius: 20,
    maxHeight: "70%",
  },
  modalHeader: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "center",
    padding: 20,
    borderBottomWidth: 1,
    borderBottomColor: "#eee",
  },
  modalTitle: {
    fontSize: 18,
    fontWeight: "bold",
    color: "#333",
  },
  modalList: {
    maxHeight: 400,
  },
  modalItem: {
    padding: 15,
    borderBottomWidth: 1,
    borderBottomColor: "#eee",
  },
  presentacionItem: {
    flex: 1,
  },
  presentacionTitle: {
    fontSize: 16,
    fontWeight: "600",
    color: "#333",
    marginBottom: 4,
  },
  presentacionSubtitle: {
    fontSize: 14,
    color: "#666",
  },
})
