"use client"

import { useState } from "react"
import {
  View,
  Text,
  StyleSheet,
  Image,
  TextInput,
  TouchableOpacity,
  Alert,
  ActivityIndicator,
  ScrollView,
} from "react-native"
import { Link, useRouter } from "expo-router"
import { MaterialCommunityIcons } from "@expo/vector-icons"
import { Picker } from "@react-native-picker/picker"
import { generateFastApiUrl } from "../utils"

import FondoImage from "../assets/images/fondo.jpg"
import LogoImage from "../assets/images/logo.jpg"

// Solo permitir roles de donante y beneficiario según la tabla de la base de datos
const ROLES = [
  { label: "Seleccionar perfil", value: "" },
  { label: "Donante", value: "4" }, // ID 4 según la tabla
  { label: "Beneficiario", value: "5" }, // ID 5 según la tabla
]

const TIPOS = [
  { label: "Seleccionar tipo", value: "" },
  { label: "Persona Física", value: "Persona Física" },
  { label: "Persona Moral", value: "Persona Moral" },
]

export default function RegisterScreen() {
  const [formData, setFormData] = useState({
    tipo: "",
    nombre: "",
    ap: "", // apellido paterno
    aM: "", // apellido materno
    edad: "",
    telefono: "",
    correo: "",
    contraseña: "",
    rfc: "",
    paginaWeb: "",
    fundacion: "",
    rol_id: "",
  })
  const [confirmPassword, setConfirmPassword] = useState("")
  const [loading, setLoading] = useState(false)
  const router = useRouter()

  const updateFormData = (field: string, value: string) => {
    setFormData((prev) => ({ ...prev, [field]: value }))
  }

  const handleRegister = async () => {
    // Validaciones básicas
    if (!formData.tipo || !formData.nombre || !formData.correo || !formData.contraseña || !formData.rol_id) {
      Alert.alert("Error", "Por favor, completa todos los campos obligatorios.")
      return
    }
    if (formData.contraseña !== confirmPassword) {
      Alert.alert("Error", "Las contraseñas no coinciden.")
      return
    }

    setLoading(true)
    try {
      const apiUrl = generateFastApiUrl("/auth/register")
      console.log("Attempting registration to:", apiUrl)

      // Preparar datos para envío
      const registrationData = {
        ...formData,
        rol_id: Number.parseInt(formData.rol_id),
        edad: formData.edad ? Number.parseInt(formData.edad) : null,
        aprobacion: false, // Por defecto no aprobado, requiere aprobación del admin
        estatus_id: 1, // Asumiendo que 1 es "activo" o el estatus por defecto
      }

      const response = await fetch(apiUrl, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(registrationData),
      })

      const data = await response.json()

      if (response.ok) {
        Alert.alert(
          "Registro Exitoso",
          "Tu solicitud de registro ha sido enviada. Un administrador revisará y aprobará tu cuenta pronto. Te notificaremos cuando puedas iniciar sesión.",
          [
            {
              text: "Entendido",
              onPress: () => router.replace("/"),
            },
          ],
        )
      } else {
        Alert.alert("Error de registro", data.detail || "Ocurrió un error al registrarse.")
      }
    } catch (error: any) {
      console.error("Error al registrar:", error)
      Alert.alert("Error de conexión", "No se pudo conectar con el servidor. " + error.message)
    } finally {
      setLoading(false)
    }
  }

  return (
    <View style={styles.containerWithBackground}>
      <Image source={FondoImage} style={styles.backgroundImage} resizeMode="cover" />
      <ScrollView style={styles.scrollContainer}>
        <View style={styles.overlay}>
          <Link href="/" asChild>
            <TouchableOpacity style={styles.backButton}>
              <MaterialCommunityIcons name="arrow-left" size={24} color="#333" />
            </TouchableOpacity>
          </Link>

          <Image source={LogoImage} style={styles.logo} resizeMode="contain" />

          <Text style={styles.title}>Crear nueva cuenta</Text>

          {/* Tipo */}
          <View style={styles.pickerContainer}>
            <Picker
              selectedValue={formData.tipo}
              onValueChange={(itemValue: string) => updateFormData("tipo", itemValue)}
              style={styles.picker}
              itemStyle={styles.pickerItem}
            >
              {TIPOS.map((tipo) => (
                <Picker.Item key={tipo.value} label={tipo.label} value={tipo.value} />
              ))}
            </Picker>
            <MaterialCommunityIcons name="chevron-down" size={20} color="#888" style={styles.pickerIcon} />
          </View>

          {/* Nombre */}
          <View style={styles.inputContainer}>
            <MaterialCommunityIcons name="account" size={20} color="#888" style={styles.inputIcon} />
            <TextInput
              style={styles.input}
              placeholder="Nombre *"
              value={formData.nombre}
              onChangeText={(value) => updateFormData("nombre", value)}
            />
          </View>

          {/* Apellido Paterno */}
          <View style={styles.inputContainer}>
            <MaterialCommunityIcons name="account" size={20} color="#888" style={styles.inputIcon} />
            <TextInput
              style={styles.input}
              placeholder="Apellido Paterno"
              value={formData.ap}
              onChangeText={(value) => updateFormData("ap", value)}
            />
          </View>

          {/* Apellido Materno */}
          <View style={styles.inputContainer}>
            <MaterialCommunityIcons name="account" size={20} color="#888" style={styles.inputIcon} />
            <TextInput
              style={styles.input}
              placeholder="Apellido Materno"
              value={formData.aM}
              onChangeText={(value) => updateFormData("aM", value)}
            />
          </View>

          {/* Edad */}
          <View style={styles.inputContainer}>
            <MaterialCommunityIcons name="calendar" size={20} color="#888" style={styles.inputIcon} />
            <TextInput
              style={styles.input}
              placeholder="Edad"
              value={formData.edad}
              onChangeText={(value) => updateFormData("edad", value)}
              keyboardType="numeric"
            />
          </View>

          {/* Teléfono */}
          <View style={styles.inputContainer}>
            <MaterialCommunityIcons name="phone" size={20} color="#888" style={styles.inputIcon} />
            <TextInput
              style={styles.input}
              placeholder="Teléfono"
              value={formData.telefono}
              onChangeText={(value) => updateFormData("telefono", value)}
              keyboardType="phone-pad"
            />
          </View>

          {/* Correo */}
          <View style={styles.inputContainer}>
            <MaterialCommunityIcons name="email" size={20} color="#888" style={styles.inputIcon} />
            <TextInput
              style={styles.input}
              placeholder="Correo electrónico *"
              value={formData.correo}
              onChangeText={(value) => updateFormData("correo", value)}
              keyboardType="email-address"
              autoCapitalize="none"
            />
          </View>

          {/* RFC */}
          <View style={styles.inputContainer}>
            <MaterialCommunityIcons name="card-account-details" size={20} color="#888" style={styles.inputIcon} />
            <TextInput
              style={styles.input}
              placeholder="RFC"
              value={formData.rfc}
              onChangeText={(value) => updateFormData("rfc", value.toUpperCase())}
              autoCapitalize="characters"
            />
          </View>

          {/* Página Web */}
          <View style={styles.inputContainer}>
            <MaterialCommunityIcons name="web" size={20} color="#888" style={styles.inputIcon} />
            <TextInput
              style={styles.input}
              placeholder="Página Web"
              value={formData.paginaWeb}
              onChangeText={(value) => updateFormData("paginaWeb", value)}
              autoCapitalize="none"
            />
          </View>

          {/* Fundación */}
          <View style={styles.inputContainer}>
            <MaterialCommunityIcons name="domain" size={20} color="#888" style={styles.inputIcon} />
            <TextInput
              style={styles.input}
              placeholder="Fundación/Organización"
              value={formData.fundacion}
              onChangeText={(value) => updateFormData("fundacion", value)}
            />
          </View>

          {/* Contraseña */}
          <View style={styles.inputContainer}>
            <MaterialCommunityIcons name="lock" size={20} color="#888" style={styles.inputIcon} />
            <TextInput
              style={styles.input}
              placeholder="Contraseña *"
              value={formData.contraseña}
              onChangeText={(value) => updateFormData("contraseña", value)}
              secureTextEntry
            />
          </View>

          {/* Confirmar Contraseña */}
          <View style={styles.inputContainer}>
            <MaterialCommunityIcons name="lock" size={20} color="#888" style={styles.inputIcon} />
            <TextInput
              style={styles.input}
              placeholder="Confirmar contraseña *"
              value={confirmPassword}
              onChangeText={setConfirmPassword}
              secureTextEntry
            />
          </View>

          {/* Rol */}
          <View style={styles.pickerContainer}>
            <Picker
              selectedValue={formData.rol_id}
              onValueChange={(itemValue: string) => updateFormData("rol_id", itemValue)}
              style={styles.picker}
              itemStyle={styles.pickerItem}
            >
              {ROLES.map((role) => (
                <Picker.Item key={role.value} label={role.label} value={role.value} />
              ))}
            </Picker>
            <MaterialCommunityIcons name="chevron-down" size={20} color="#888" style={styles.pickerIcon} />
          </View>

          <TouchableOpacity style={styles.registerButton} onPress={handleRegister} disabled={loading}>
            {loading ? (
              <ActivityIndicator color="#fff" />
            ) : (
              <Text style={styles.registerButtonText}>Enviar Solicitud</Text>
            )}
          </TouchableOpacity>

          <Text style={styles.infoText}>
            * Campos obligatorios{"\n"}
            Tu cuenta será revisada por un administrador antes de ser activada.
          </Text>

          <Text style={styles.footerText}>© 2025 MACRA Banco de Alimentos</Text>
        </View>
      </ScrollView>
    </View>
  )
}

const styles = StyleSheet.create({
  containerWithBackground: {
    flex: 1,
  },
  backgroundImage: {
    position: "absolute",
    top: 0,
    left: 0,
    bottom: 0,
    right: 0,
    width: "100%",
    height: "100%",
  },
  scrollContainer: {
    flex: 1,
  },
  overlay: {
    backgroundColor: "rgba(255, 255, 255, 0.8)",
    alignItems: "center",
    paddingTop: 60,
    paddingHorizontal: 20,
    paddingBottom: 40,
    minHeight: "100%",
  },
  backButton: {
    position: "absolute",
    top: 40,
    left: 20,
    zIndex: 1,
    backgroundColor: "white",
    borderRadius: 20,
    padding: 8,
  },
  logo: {
    width: 100,
    height: 100,
    marginBottom: 20,
  },
  title: {
    fontSize: 24,
    fontWeight: "bold",
    color: "#333",
    marginBottom: 20,
  },
  inputContainer: {
    flexDirection: "row",
    alignItems: "center",
    backgroundColor: "white",
    borderRadius: 25,
    paddingHorizontal: 15,
    marginBottom: 15,
    width: "90%",
    height: 50,
    shadowColor: "#000",
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 3,
    elevation: 3,
  },
  inputIcon: {
    marginRight: 10,
  },
  input: {
    flex: 1,
    fontSize: 16,
    color: "#333",
  },
  pickerContainer: {
    flexDirection: "row",
    alignItems: "center",
    backgroundColor: "white",
    borderRadius: 25,
    marginBottom: 15,
    width: "90%",
    height: 50,
    shadowColor: "#000",
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 3,
    elevation: 3,
    overflow: "hidden",
  },
  picker: {
    flex: 1,
    height: "100%",
    color: "#333",
  },
  pickerItem: {
    fontSize: 16,
  },
  pickerIcon: {
    position: "absolute",
    right: 15,
    top: 15,
  },
  registerButton: {
    backgroundColor: "#8B4513",
    paddingVertical: 15,
    borderRadius: 25,
    width: "90%",
    alignItems: "center",
    marginTop: 10,
    marginBottom: 20,
  },
  registerButtonText: {
    color: "white",
    fontSize: 18,
    fontWeight: "bold",
  },
  infoText: {
    color: "#666",
    fontSize: 14,
    textAlign: "center",
    marginBottom: 20,
    lineHeight: 20,
  },
  footerText: {
    color: "#666",
    fontSize: 14,
    marginBottom: 20,
  },
})
