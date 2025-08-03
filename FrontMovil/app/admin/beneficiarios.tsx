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

interface Beneficiario {
  id: number
  nombre: string
  aP: string
  aM: string
  correo: string
  telefono: string
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

export default function BeneficiariosScreen() {
  const [beneficiarios, setBeneficiarios] = useState<Beneficiario[]>([])
  const [estatuses, setEstatuses] = useState<Estatus[]>([])
  const [loading, setLoading] = useState(true)
  const [refreshing, setRefreshing] = useState(false)
  const [modalVisible, setModalVisible] = useState(false)
  const [editingBeneficiario, setEditingBeneficiario] = useState<Beneficiario | null>(null)
  const [formData, setFormData] = useState({
    nombre: "",
    aP: "",
    aM: "",
    correo: "",
    telefono: "",
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

      // Cargar beneficiarios
      const beneficiariosResponse = await fetch(generateFastApiUrl("/usuarios/"), {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      })

      if (beneficiariosResponse.ok) {
        const allUsers = await beneficiariosResponse.json()
        // Filtrar solo beneficiarios (rol_id = 3)
        const beneficiariosData = allUsers.filter((user: Beneficiario) => user.rol_id === 3 && !user.del_flag)
        setBeneficiarios(beneficiariosData)
      }

      // Cargar estatuses
      // const estatusResponse = await fetch(generateFastApiUrl("/estatus/"), {
      //   headers: {
      //     Authorization: `Bearer ${token}`,
      //   },
      // })

      // if (estatusResponse.ok) {
      //   const estatusData = await estatusResponse.json()
      //   setEstatuses(estatusData)
      // }
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
    setEditingBeneficiario(null)
    setFormData({
      nombre: "",
      aP: "",
      aM: "",
      correo: "",
      telefono: "",
      paginaWeb: "",
      contraseña: "",
      estatus_id: 1,
    })
    setModalVisible(true)
  }

  const openEditModal = (beneficiario: Beneficiario) => {
    setEditingBeneficiario(beneficiario)
    setFormData({
      nombre: beneficiario.nombre,
      aP: beneficiario.aP,
      aM: beneficiario.aM,
      correo: beneficiario.correo,
      telefono: beneficiario.telefono,
      paginaWeb: beneficiario.paginaWeb || "",
      contraseña: "",
      estatus_id: beneficiario.estatus_id,
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

      if (!formData.nombre || !formData.aP || !formData.aM || !formData.correo || !formData.telefono) {
        Alert.alert("Error", "Por favor completa todos los campos obligatorios")
        return
      }

      const url = editingBeneficiario
        ? generateFastApiUrl(`/usuarios/${editingBeneficiario.id}`)
        : generateFastApiUrl("/usuarios/")

      const method = editingBeneficiario ? "PUT" : "POST"

      // Para crear: no incluir estatus_id (siempre será 1 - Activo)
      // Para editar: incluir estatus_id
      const requestData = editingBeneficiario
        ? {
            nombre: formData.nombre,
            aP: formData.aP,
            aM: formData.aM,
            correo: formData.correo,
            telefono: formData.telefono,
            paginaWeb: formData.paginaWeb,
            estatus_id: formData.estatus_id,
            ...(formData.contraseña && { contraseña: formData.contraseña }),
          }
        : {
            nombre: formData.nombre,
            aP: formData.aP,
            aM: formData.aM,
            correo: formData.correo,
            telefono: formData.telefono,
            paginaWeb: formData.paginaWeb,
            contraseña: formData.contraseña,
            rol_id: 3, // Beneficiario
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
        Alert.alert("Éxito", editingBeneficiario ? "Beneficiario actualizado" : "Beneficiario creado")
        setModalVisible(false)
        loadData()
      } else {
        const errorData = await response.text()
        console.error("Error response:", errorData)
        Alert.alert("Error", "Error al guardar el beneficiario")
      }
    } catch (error) {
      console.error("Error saving beneficiario:", error)
      Alert.alert("Error", "Error de conexión")
    }
  }

  const handleDelete = async (id: number) => {
    Alert.alert("Confirmar", "¿Estás seguro de que quieres eliminar este beneficiario?", [
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
              Alert.alert("Éxito", "Beneficiario eliminado")
              loadData()
            } else {
              Alert.alert("Error", "Error al eliminar el beneficiario")
            }
          } catch (error) {
            console.error("Error deleting beneficiario:", error)
            Alert.alert("Error", "Error de conexión")
          }
        },
      },
    ])
  }

  const renderBeneficiario = ({ item }: { item: Beneficiario }) => (
    <View style={styles.beneficiarioCard}>
      <View style={styles.beneficiarioInfo}>
        <Text style={styles.beneficiarioName}>
          {item.nombre} {item.aP} {item.aM}
        </Text>
        <Text style={styles.beneficiarioEmail}>{item.correo}</Text>
        <Text style={styles.beneficiarioPhone}>{item.telefono}</Text>
        {item.paginaWeb && <Text style={styles.beneficiarioWebsite}>{item.paginaWeb}</Text>}
        <View style={styles.statusContainer}>
          <View style={[styles.statusBadge, { backgroundColor: item.estatus_id === 1 ? "#4CAF50" : "#F44336" }]}>
            <Text style={styles.statusText}>{item.estatus_id === 1 ? "Activo" : "Inactivo"}</Text>
          </View>
        </View>
      </View>
      <View style={styles.beneficiarioActions}>
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
        <Text style={styles.loadingText}>Cargando beneficiarios...</Text>
      </View>
    )
  }

  return (
    <View style={styles.container}>
      <View style={styles.header}>
        <TouchableOpacity style={styles.backButton} onPress={() => router.back()}>
          <MaterialCommunityIcons name="arrow-left" size={24} color="#fff" />
        </TouchableOpacity>
        <Text style={styles.headerTitle}>Gestión de Beneficiarios</Text>
        <TouchableOpacity style={styles.addButton} onPress={openCreateModal}>
          <MaterialCommunityIcons name="plus" size={24} color="#fff" />
        </TouchableOpacity>
      </View>

      <FlatList
        data={beneficiarios}
        renderItem={renderBeneficiario}
        keyExtractor={(item) => item.id.toString()}
        contentContainerStyle={styles.listContainer}
        refreshControl={<RefreshControl refreshing={refreshing} onRefresh={onRefresh} colors={["#8B4513"]} />}
        ListEmptyComponent={
          <View style={styles.emptyContainer}>
            <MaterialCommunityIcons name="account-heart" size={64} color="#ccc" />
            <Text style={styles.emptyText}>No hay beneficiarios registrados</Text>
          </View>
        }
      />

      <Modal visible={modalVisible} animationType="slide" transparent>
        <View style={styles.modalOverlay}>
          <View style={styles.modalContent}>
            <ScrollView>
              <View style={styles.modalHeader}>
                <Text style={styles.modalTitle}>
                  {editingBeneficiario ? "Editar Beneficiario" : "Crear Beneficiario"}
                </Text>
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
                <Text style={styles.label}>Página Web</Text>
                <TextInput
                  style={styles.input}
                  value={formData.paginaWeb}
                  onChangeText={(text) => setFormData({ ...formData, paginaWeb: text })}
                  placeholder="https://ejemplo.com"
                  autoCapitalize="none"
                />
              </View>

              <View style={styles.formGroup}>
                <Text style={styles.label}>{editingBeneficiario ? "Nueva Contraseña (opcional)" : "Contraseña *"}</Text>
                <TextInput
                  style={styles.input}
                  value={formData.contraseña}
                  onChangeText={(text) => setFormData({ ...formData, contraseña: text })}
                  placeholder="Contraseña"
                  secureTextEntry
                />
              </View>

              {editingBeneficiario && (
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
  beneficiarioCard: {
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
  beneficiarioInfo: {
    flex: 1,
  },
  beneficiarioName: {
    fontSize: 18,
    fontWeight: "bold",
    color: "#333",
    marginBottom: 4,
  },
  beneficiarioEmail: {
    fontSize: 14,
    color: "#666",
    marginBottom: 2,
  },
  beneficiarioPhone: {
    fontSize: 14,
    color: "#666",
    marginBottom: 2,
  },
  beneficiarioWebsite: {
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
  beneficiarioActions: {
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
