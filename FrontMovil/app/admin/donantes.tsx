"use client"

import { useState, useEffect } from "react"
import {
  View,
  Text,
  StyleSheet,
  TouchableOpacity,
  FlatList,
  Alert,
  Modal,
  TextInput,
  ActivityIndicator,
  RefreshControl,
  ScrollView,
} from "react-native"
import { useRouter } from "expo-router"
import AsyncStorage from "@react-native-async-storage/async-storage"
import { MaterialCommunityIcons } from "@expo/vector-icons"
import { generateFastApiUrl } from "@/utils"

interface Donante {
  id: number
  nombre: string
  aP: string
  aM: string
  correo: string
  telefono: string
  rfc?: string
  edad?: number
  paginaWeb?: string
  estatus_id: number
  rol_id: number
  aprobacion: boolean
  del_flag: boolean
}

interface Estatus {
  id: number
  nombre: string
}

export default function DonantesScreen() {
  const [donantes, setDonantes] = useState<Donante[]>([])
  const [estatuses, setEstatuses] = useState<Estatus[]>([])
  const [loading, setLoading] = useState(true)
  const [refreshing, setRefreshing] = useState(false)
  const [modalVisible, setModalVisible] = useState(false)
  const [editingDonante, setEditingDonante] = useState<Donante | null>(null)
  const [formData, setFormData] = useState({
    nombre: "",
    aP: "",
    aM: "",
    correo: "",
    telefono: "",
    rfc: "",
    edad: "",
    paginaWeb: "",
    contraseña: "",
    estatus_id: 1,
  })
  const router = useRouter()

  useEffect(() => {
    checkAuth()
  }, [])

  const checkAuth = async () => {
    try {
      const token = await AsyncStorage.getItem("token")
      if (!token) {
        router.replace("/login")
        return
      }
      loadData()
    } catch (error) {
      console.error("Error checking auth:", error)
      router.replace("/login")
    }
  }

  const loadData = async () => {
    try {
      const token = await AsyncStorage.getItem("token")
      if (!token) {
        router.replace("/login")
        return
      }

      // Cargar donantes
      const donantesResponse = await fetch(generateFastApiUrl("/usuarios/"), {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      })

      if (donantesResponse.ok) {
        const allUsers = await donantesResponse.json()
        // Filtrar solo donantes (rol_id = 2)
        const donantesData = allUsers.filter((user: Donante) => user.rol_id === 2 && !user.del_flag)
        setDonantes(donantesData)
      }
    } catch (error) {
      console.error("Error loading data:", error)
      Alert.alert("Error", "Error al cargar los datos")
    } finally {
      setLoading(false)
      setRefreshing(false)
    }
  }

  const onRefresh = () => {
    setRefreshing(true)
    loadData()
  }

  const openCreateModal = () => {
    setEditingDonante(null)
    setFormData({
      nombre: "",
      aP: "",
      aM: "",
      correo: "",
      telefono: "",
      rfc: "",
      edad: "",
      paginaWeb: "",
      contraseña: "",
      estatus_id: 1,
    })
    setModalVisible(true)
  }

  const openEditModal = (donante: Donante) => {
    setEditingDonante(donante)
    setFormData({
      nombre: donante.nombre,
      aP: donante.aP,
      aM: donante.aM,
      correo: donante.correo,
      telefono: donante.telefono,
      rfc: donante.rfc || "",
      edad: donante.edad ? donante.edad.toString() : "",
      paginaWeb: donante.paginaWeb || "",
      contraseña: "",
      estatus_id: donante.estatus_id,
    })
    setModalVisible(true)
  }

  const handleSave = async () => {
    try {
      const token = await AsyncStorage.getItem("token")
      if (!token) {
        router.replace("/login")
        return
      }

      if (!formData.nombre || !formData.aP || !formData.aM || !formData.correo || !formData.telefono || !formData.rfc) {
        Alert.alert("Error", "Por favor completa todos los campos obligatorios")
        return
      }

      // Validar formato de email
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
      if (!emailRegex.test(formData.correo)) {
        Alert.alert("Error", "Por favor ingresa un correo electrónico válido")
        return
      }

      // Validar edad si se proporciona
      if (formData.edad && (isNaN(Number(formData.edad)) || Number(formData.edad) < 1 || Number(formData.edad) > 120)) {
        Alert.alert("Error", "Por favor ingresa una edad válida (1-120)")
        return
      }

      const url = editingDonante
        ? generateFastApiUrl(`/usuarios/${editingDonante.id}`)
        : generateFastApiUrl("/usuarios/")

      const method = editingDonante ? "PUT" : "POST"

      const requestData = editingDonante
        ? {
            nombre: formData.nombre,
            aP: formData.aP,
            aM: formData.aM,
            correo: formData.correo,
            telefono: formData.telefono,
            rfc: formData.rfc,
            ...(formData.edad && { edad: Number(formData.edad) }),
            paginaWeb: formData.paginaWeb,
            estatus_id: formData.estatus_id,
            ...(formData.contraseña && { contraseña: formData.contraseña }),
          }
        : {
            tipo: "persona",
            nombre: formData.nombre,
            aP: formData.aP,
            aM: formData.aM,
            correo: formData.correo,
            telefono: formData.telefono,
            rfc: formData.rfc,
            ...(formData.edad && { edad: Number(formData.edad) }),
            paginaWeb: formData.paginaWeb,
            contraseña: formData.contraseña,
            rol_id: 2, // Donante
            aprobacion: true,
            del_flag: false,
          }

      const response = await fetch(url, {
        method,
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${token}`,
        },
        body: JSON.stringify(requestData),
      })

      if (response.ok) {
        Alert.alert("Éxito", editingDonante ? "Donante actualizado" : "Donante creado")
        setModalVisible(false)
        loadData()
      } else {
        const errorData = await response.text()
        console.error("Error response:", errorData)
        Alert.alert("Error", "Error al guardar el donante")
      }
    } catch (error) {
      console.error("Error saving donante:", error)
      Alert.alert("Error", "Error de conexión")
    }
  }

  const handleDelete = async (id: number) => {
    Alert.alert("Confirmar", "¿Estás seguro de que quieres eliminar este donante?", [
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

            const response = await fetch(generateFastApiUrl(`/usuarios/${id}`), {
              method: "DELETE",
              headers: {
                Authorization: `Bearer ${token}`,
              },
            })

            if (response.ok) {
              Alert.alert("Éxito", "Donante eliminado")
              loadData()
            } else {
              Alert.alert("Error", "Error al eliminar el donante")
            }
          } catch (error) {
            console.error("Error deleting donante:", error)
            Alert.alert("Error", "Error de conexión")
          }
        },
      },
    ])
  }

  const renderDonante = ({ item }: { item: Donante }) => (
    <View style={styles.donanteCard}>
      <View style={styles.donanteInfo}>
        <Text style={styles.donanteName}>
          {item.nombre} {item.aP} {item.aM}
        </Text>
        <Text style={styles.donanteEmail}>{item.correo}</Text>
        <Text style={styles.donantePhone}>{item.telefono}</Text>
        {item.rfc && <Text style={styles.donanteRfc}>RFC: {item.rfc}</Text>}
        {item.edad && <Text style={styles.donanteAge}>Edad: {item.edad} años</Text>}
        {item.paginaWeb && <Text style={styles.donanteWebsite}>{item.paginaWeb}</Text>}
        <View style={styles.statusContainer}>
          <View style={[styles.statusBadge, { backgroundColor: item.estatus_id === 1 ? "#4CAF50" : "#F44336" }]}>
            <Text style={styles.statusText}>{item.estatus_id === 1 ? "Activo" : "Inactivo"}</Text>
          </View>
        </View>
      </View>
      <View style={styles.donanteActions}>
        <TouchableOpacity style={styles.editButton} onPress={() => openEditModal(item)}>
          <MaterialCommunityIcons name="pencil" size={20} color="#fff" />
        </TouchableOpacity>
        <TouchableOpacity style={styles.deleteButton} onPress={() => handleDelete(item.id)}>
          <MaterialCommunityIcons name="delete" size={20} color="#fff" />
        </TouchableOpacity>
      </View>
    </View>
  )

  if (loading) {
    return (
      <View style={styles.loadingContainer}>
        <ActivityIndicator size="large" color="#8B4513" />
        <Text style={styles.loadingText}>Cargando donantes...</Text>
      </View>
    )
  }

  return (
    <View style={styles.container}>
      <View style={styles.header}>
        <TouchableOpacity style={styles.backButton} onPress={() => router.back()}>
          <MaterialCommunityIcons name="arrow-left" size={24} color="#fff" />
        </TouchableOpacity>
        <Text style={styles.headerTitle}>Gestión de Donantes</Text>
        <TouchableOpacity style={styles.addButton} onPress={openCreateModal}>
          <MaterialCommunityIcons name="plus" size={24} color="#fff" />
        </TouchableOpacity>
      </View>

      <FlatList
        data={donantes}
        renderItem={renderDonante}
        keyExtractor={(item) => item.id.toString()}
        contentContainerStyle={styles.listContainer}
        refreshControl={<RefreshControl refreshing={refreshing} onRefresh={onRefresh} colors={["#8B4513"]} />}
        ListEmptyComponent={
          <View style={styles.emptyContainer}>
            <MaterialCommunityIcons name="account-group" size={64} color="#ccc" />
            <Text style={styles.emptyText}>No hay donantes registrados</Text>
          </View>
        }
      />

      <Modal visible={modalVisible} animationType="slide" transparent>
        <View style={styles.modalOverlay}>
          <View style={styles.modalContent}>
            <ScrollView>
              <View style={styles.modalHeader}>
                <Text style={styles.modalTitle}>{editingDonante ? "Editar Donante" : "Crear Donante"}</Text>
                <TouchableOpacity onPress={() => setModalVisible(false)}>
                  <MaterialCommunityIcons name="close" size={24} color="#666" />
                </TouchableOpacity>
              </View>

              <View style={styles.formGroup}>
                <Text style={styles.label}>Nombre *</Text>
                <TextInput
                  style={styles.input}
                  value={formData.nombre}
                  onChangeText={(text) => setFormData({ ...formData, nombre: text })}
                  placeholder="Nombre"
                />
              </View>

              <View style={styles.formGroup}>
                <Text style={styles.label}>Apellido Paterno *</Text>
                <TextInput
                  style={styles.input}
                  value={formData.aP}
                  onChangeText={(text) => setFormData({ ...formData, aP: text })}
                  placeholder="Apellido Paterno"
                />
              </View>

              <View style={styles.formGroup}>
                <Text style={styles.label}>Apellido Materno *</Text>
                <TextInput
                  style={styles.input}
                  value={formData.aM}
                  onChangeText={(text) => setFormData({ ...formData, aM: text })}
                  placeholder="Apellido Materno"
                />
              </View>

              <View style={styles.formGroup}>
                <Text style={styles.label}>Correo *</Text>
                <TextInput
                  style={styles.input}
                  value={formData.correo}
                  onChangeText={(text) => setFormData({ ...formData, correo: text })}
                  placeholder="correo@ejemplo.com"
                  keyboardType="email-address"
                  autoCapitalize="none"
                  autoCorrect={false}
                />
              </View>

              <View style={styles.formGroup}>
                <Text style={styles.label}>Teléfono *</Text>
                <TextInput
                  style={styles.input}
                  value={formData.telefono}
                  onChangeText={(text) => setFormData({ ...formData, telefono: text })}
                  placeholder="Teléfono"
                  keyboardType="phone-pad"
                />
              </View>

              <View style={styles.formGroup}>
                <Text style={styles.label}>RFC *</Text>
                <TextInput
                  style={styles.input}
                  value={formData.rfc}
                  onChangeText={(text) => setFormData({ ...formData, rfc: text.toUpperCase() })}
                  placeholder="RFC"
                  autoCapitalize="characters"
                  maxLength={13}
                />
              </View>

              <View style={styles.formGroup}>
                <Text style={styles.label}>Edad</Text>
                <TextInput
                  style={styles.input}
                  value={formData.edad}
                  onChangeText={(text) => setFormData({ ...formData, edad: text })}
                  placeholder="Edad"
                  keyboardType="numeric"
                  maxLength={3}
                />
              </View>

              <View style={styles.formGroup}>
                <Text style={styles.label}>Página Web</Text>
                <TextInput
                  style={styles.input}
                  value={formData.paginaWeb}
                  onChangeText={(text) => setFormData({ ...formData, paginaWeb: text })}
                  placeholder="https://ejemplo.com"
                  autoCapitalize="none"
                  autoCorrect={false}
                />
              </View>

              <View style={styles.formGroup}>
                <Text style={styles.label}>{editingDonante ? "Nueva Contraseña (opcional)" : "Contraseña *"}</Text>
                <TextInput
                  style={styles.input}
                  value={formData.contraseña}
                  onChangeText={(text) => setFormData({ ...formData, contraseña: text })}
                  placeholder="Contraseña"
                  secureTextEntry
                />
              </View>

              {editingDonante && (
                <View style={styles.formGroup}>
                  <Text style={styles.label}>Estatus</Text>
                  <View style={styles.statusSelector}>
                    <TouchableOpacity
                      style={[styles.statusOption, formData.estatus_id === 1 && styles.statusOptionSelected]}
                      onPress={() => setFormData({ ...formData, estatus_id: 1 })}
                    >
                      <Text
                        style={[styles.statusOptionText, formData.estatus_id === 1 && styles.statusOptionTextSelected]}
                      >
                        Activo
                      </Text>
                    </TouchableOpacity>
                    <TouchableOpacity
                      style={[styles.statusOption, formData.estatus_id === 2 && styles.statusOptionSelected]}
                      onPress={() => setFormData({ ...formData, estatus_id: 2 })}
                    >
                      <Text
                        style={[styles.statusOptionText, formData.estatus_id === 2 && styles.statusOptionTextSelected]}
                      >
                        Inactivo
                      </Text>
                    </TouchableOpacity>
                  </View>
                </View>
              )}

              <View style={styles.modalActions}>
                <TouchableOpacity style={styles.cancelButton} onPress={() => setModalVisible(false)}>
                  <Text style={styles.cancelButtonText}>Cancelar</Text>
                </TouchableOpacity>
                <TouchableOpacity style={styles.saveButton} onPress={handleSave}>
                  <Text style={styles.saveButtonText}>Guardar</Text>
                </TouchableOpacity>
              </View>
            </ScrollView>
          </View>
        </View>
      </Modal>
    </View>
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
    backgroundColor: "#8B4513",
    flexDirection: "row",
    alignItems: "center",
    justifyContent: "space-between",
    paddingHorizontal: 16,
    paddingTop: 50,
    paddingBottom: 16,
  },
  backButton: {
    padding: 8,
  },
  headerTitle: {
    fontSize: 20,
    fontWeight: "bold",
    color: "#fff",
    flex: 1,
    textAlign: "center",
  },
  addButton: {
    padding: 8,
  },
  listContainer: {
    padding: 16,
  },
  donanteCard: {
    backgroundColor: "#fff",
    borderRadius: 12,
    padding: 16,
    marginBottom: 12,
    flexDirection: "row",
    alignItems: "center",
    shadowColor: "#000",
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 3,
  },
  donanteInfo: {
    flex: 1,
  },
  donanteName: {
    fontSize: 18,
    fontWeight: "bold",
    color: "#333",
    marginBottom: 4,
  },
  donanteEmail: {
    fontSize: 14,
    color: "#666",
    marginBottom: 2,
  },
  donantePhone: {
    fontSize: 14,
    color: "#666",
    marginBottom: 2,
  },
  donanteRfc: {
    fontSize: 14,
    color: "#666",
    marginBottom: 2,
  },
  donanteAge: {
    fontSize: 14,
    color: "#666",
    marginBottom: 2,
  },
  donanteWebsite: {
    fontSize: 14,
    color: "#8B4513",
    marginBottom: 8,
  },
  statusContainer: {
    flexDirection: "row",
    alignItems: "center",
  },
  statusBadge: {
    paddingHorizontal: 8,
    paddingVertical: 4,
    borderRadius: 12,
  },
  statusText: {
    color: "#fff",
    fontSize: 12,
    fontWeight: "bold",
  },
  donanteActions: {
    flexDirection: "row",
    gap: 8,
  },
  editButton: {
    backgroundColor: "#2196F3",
    padding: 8,
    borderRadius: 8,
  },
  deleteButton: {
    backgroundColor: "#F44336",
    padding: 8,
    borderRadius: 8,
  },
  emptyContainer: {
    flex: 1,
    justifyContent: "center",
    alignItems: "center",
    paddingTop: 100,
  },
  emptyText: {
    fontSize: 16,
    color: "#666",
    marginTop: 16,
  },
  modalOverlay: {
    flex: 1,
    backgroundColor: "rgba(0, 0, 0, 0.5)",
    justifyContent: "center",
    alignItems: "center",
  },
  modalContent: {
    backgroundColor: "#fff",
    borderRadius: 12,
    padding: 20,
    width: "90%",
    maxHeight: "80%",
  },
  modalHeader: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "center",
    marginBottom: 20,
  },
  modalTitle: {
    fontSize: 20,
    fontWeight: "bold",
    color: "#333",
  },
  formGroup: {
    marginBottom: 16,
  },
  label: {
    fontSize: 16,
    fontWeight: "bold",
    color: "#333",
    marginBottom: 8,
  },
  input: {
    borderWidth: 1,
    borderColor: "#ddd",
    borderRadius: 8,
    paddingHorizontal: 12,
    paddingVertical: 10,
    fontSize: 16,
    backgroundColor: "#fff",
  },
  statusSelector: {
    flexDirection: "row",
    gap: 12,
  },
  statusOption: {
    flex: 1,
    paddingVertical: 12,
    paddingHorizontal: 16,
    borderRadius: 8,
    borderWidth: 1,
    borderColor: "#ddd",
    alignItems: "center",
  },
  statusOptionSelected: {
    backgroundColor: "#8B4513",
    borderColor: "#8B4513",
  },
  statusOptionText: {
    fontSize: 16,
    color: "#666",
  },
  statusOptionTextSelected: {
    color: "#fff",
    fontWeight: "bold",
  },
  modalActions: {
    flexDirection: "row",
    justifyContent: "space-between",
    marginTop: 20,
    gap: 12,
  },
  cancelButton: {
    flex: 1,
    paddingVertical: 12,
    borderRadius: 8,
    borderWidth: 1,
    borderColor: "#ddd",
    alignItems: "center",
  },
  cancelButtonText: {
    fontSize: 16,
    color: "#666",
  },
  saveButton: {
    flex: 1,
    backgroundColor: "#8B4513",
    paddingVertical: 12,
    borderRadius: 8,
    alignItems: "center",
  },
  saveButtonText: {
    fontSize: 16,
    color: "#fff",
    fontWeight: "bold",
  },
})
