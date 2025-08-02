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
import { generateFastApiUrl } from "@/utils"

import FondoImage from "../assets/images/fondo.jpg"
import LogoImage from "../assets/images/logo.jpg"

const ROLES = [
  { label: "Seleccionar perfil", value: "" },
  { label: "Donante", value: "2" },
  { label: "Beneficiario", value: "3" },
]

const TIPOS_USUARIO = [
  { label: "Seleccionar tipo", value: "" },
  { label: "Persona", value: "persona" },
  { label: "Organización", value: "organizacion" },
]

export default function RegisterScreen() {
  const [formData, setFormData] = useState({
    tipo: "",
    nombre: "",
    aP: "", // apellido paterno
    aM: "", // apellido materno - ahora obligatorio
    edad: "", // ahora obligatorio
    telefono: "",
    correo: "",
    contraseña: "",
    confirmarContraseña: "",
    rfc: "", // ahora obligatorio
    paginaWeb: "",
    rol_id: "",
  })
  const [loading, setLoading] = useState(false)
  const router = useRouter()

  const updateFormData = (field: string, value: string) => {
    setFormData((prev) => ({
      ...prev,
      [field]: value,
    }))
  }

  const validateRFC = (rfc: string) => {
    // Validación básica de RFC mexicano
    const rfcRegex = /^[A-ZÑ&]{3,4}[0-9]{6}[A-Z0-9]{3}$/
    return rfcRegex.test(rfc)
  }

  const validateForm = () => {
    const requiredFields = ["tipo", "nombre", "aP", "aM", "edad", "telefono", "correo", "contraseña", "rfc", "rol_id"]

    for (const field of requiredFields) {
      if (!formData[field as keyof typeof formData]) {
        let fieldName = field
        switch (field) {
          case "aP":
            fieldName = "Apellido Paterno"
            break
          case "aM":
            fieldName = "Apellido Materno"
            break
          case "tipo":
            fieldName = "Tipo de usuario"
            break
          case "rol_id":
            fieldName = "Perfil"
            break
          case "rfc":
            fieldName = "RFC"
            break
          default:
            fieldName = field.charAt(0).toUpperCase() + field.slice(1)
        }
        Alert.alert("Error", `El campo ${fieldName} es obligatorio.`)
        return false
      }
    }

    if (formData.contraseña !== formData.confirmarContraseña) {
      Alert.alert("Error", "Las contraseñas no coinciden.")
      return false
    }

    // Validar formato de email
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
    if (!emailRegex.test(formData.correo)) {
      Alert.alert("Error", "Por favor ingresa un correo electrónico válido.")
      return false
    }

    // Validar teléfono (solo números)
    const phoneRegex = /^[0-9]+$/
    if (!phoneRegex.test(formData.telefono)) {
      Alert.alert("Error", "El teléfono debe contener solo números.")
      return false
    }

    // Validar edad
    const edad = Number(formData.edad)
    if (isNaN(edad) || edad < 1 || edad > 120) {
      Alert.alert("Error", "La edad debe ser un número válido entre 1 y 120.")
      return false
    }

    // Validar RFC
    if (!validateRFC(formData.rfc)) {
      Alert.alert("Error", "El RFC no tiene un formato válido. Debe tener 12 o 13 caracteres (ej: ABCD123456EFG).")
      return false
    }

    return true
  }

  const handleRegister = async () => {
    if (!validateForm()) return

    setLoading(true)
    try {
      const apiUrl = generateFastApiUrl("/auth/register")

      // Preparar datos para enviar según el esquema UsuarioCreate
      const { confirmarContraseña, ...dataToSend } = formData

      const requestData = {
        tipo: dataToSend.tipo,
        nombre: dataToSend.nombre,
        aP: dataToSend.aP,
        aM: dataToSend.aM,
        edad: Number(dataToSend.edad),
        telefono: dataToSend.telefono,
        correo: dataToSend.correo,
        contraseña: dataToSend.contraseña,
        rfc: dataToSend.rfc,
        paginaWeb: dataToSend.paginaWeb || null,
        fundacion: null,
        rol_id: Number.parseInt(dataToSend.rol_id),
        estatus_id: null,
        direccion_id: null,
        aprobacion: false,
      }

      const response = await fetch(apiUrl, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(requestData),
      })

      const data = await response.json()

      if (response.ok) {
        Alert.alert(
          "Registro Exitoso",
          "Tu cuenta ha sido creada exitosamente. Un administrador debe aprobar tu cuenta antes de que puedas iniciar sesión.",
          [
            {
              text: "OK",
              onPress: () => router.replace("/login"),
            },
          ],
        )
      } else {
        let errorMessage = "Ocurrió un error al registrarse."

        if (typeof data.detail === "string") {
          errorMessage = data.detail
        } else if (Array.isArray(data.detail)) {
          errorMessage = data.detail
            .map((err: any) => {
              const field = err.loc ? err.loc.join(".") : "campo"
              return `${field}: ${err.msg}`
            })
            .join("\n")
        } else if (data.message) {
          errorMessage = data.message
        }

        Alert.alert("Error de registro", errorMessage)
      }
    } catch (error: any) {
      Alert.alert("Error de conexión", "No se pudo conectar con el servidor. Intenta nuevamente.")
    } finally {
      setLoading(false)
    }
  }

  return (
    <View style={styles.containerWithBackground}>
      <Image source={FondoImage} style={styles.backgroundImage} resizeMode="cover" />
      <ScrollView style={styles.scrollView} contentContainerStyle={styles.scrollContent}>
        <View style={styles.overlay}>
          <Link href="/" asChild>
            <TouchableOpacity style={styles.backButton}>
              <MaterialCommunityIcons name="arrow-left" size={24} color="#333" />
            </TouchableOpacity>
          </Link>

          <Image source={LogoImage} style={styles.logo} resizeMode="contain" />

          <Text style={styles.title}>Crear nueva cuenta</Text>

          <View style={styles.pickerContainer}>
            <Picker
              selectedValue={formData.tipo}
              onValueChange={(itemValue: string) => updateFormData("tipo", itemValue)}
              style={styles.picker}
              itemStyle={styles.pickerItem}
            >
              {TIPOS_USUARIO.map((tipo) => (
                <Picker.Item key={tipo.value} label={tipo.label} value={tipo.value} />
              ))}
            </Picker>
            <MaterialCommunityIcons name="chevron-down" size={20} color="#888" style={styles.pickerIcon} />
          </View>

          <View style={styles.inputContainer}>
            <MaterialCommunityIcons name="account" size={20} color="#888" style={styles.inputIcon} />
            <TextInput
              style={styles.input}
              placeholder="Nombre *"
              value={formData.nombre}
              onChangeText={(value) => updateFormData("nombre", value)}
              autoCapitalize="words"
            />
          </View>

          <View style={styles.inputContainer}>
            <MaterialCommunityIcons name="account" size={20} color="#888" style={styles.inputIcon} />
            <TextInput
              style={styles.input}
              placeholder="Apellido Paterno *"
              value={formData.aP}
              onChangeText={(value) => updateFormData("aP", value)}
              autoCapitalize="words"
            />
          </View>

          <View style={styles.inputContainer}>
            <MaterialCommunityIcons name="account" size={20} color="#888" style={styles.inputIcon} />
            <TextInput
              style={styles.input}
              placeholder="Apellido Materno *"
              value={formData.aM}
              onChangeText={(value) => updateFormData("aM", value)}
              autoCapitalize="words"
            />
          </View>

          <View style={styles.inputContainer}>
            <MaterialCommunityIcons name="calendar" size={20} color="#888" style={styles.inputIcon} />
            <TextInput
              style={styles.input}
              placeholder="Edad *"
              value={formData.edad}
              onChangeText={(value) => updateFormData("edad", value)}
              keyboardType="numeric"
            />
          </View>

          <View style={styles.inputContainer}>
            <MaterialCommunityIcons name="phone" size={20} color="#888" style={styles.inputIcon} />
            <TextInput
              style={styles.input}
              placeholder="Teléfono *"
              value={formData.telefono}
              onChangeText={(value) => updateFormData("telefono", value)}
              keyboardType="phone-pad"
            />
          </View>

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

          <View style={styles.inputContainer}>
            <MaterialCommunityIcons name="card-account-details" size={20} color="#888" style={styles.inputIcon} />
            <TextInput
              style={styles.input}
              placeholder="RFC *"
              value={formData.rfc}
              onChangeText={(value) => updateFormData("rfc", value.toUpperCase())}
              autoCapitalize="characters"
              maxLength={13}
            />
          </View>

          <View style={styles.inputContainer}>
            <MaterialCommunityIcons name="web" size={20} color="#888" style={styles.inputIcon} />
            <TextInput
              style={styles.input}
              placeholder="Página web (opcional)"
              value={formData.paginaWeb}
              onChangeText={(value) => updateFormData("paginaWeb", value)}
              keyboardType="url"
              autoCapitalize="none"
            />
          </View>

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

          <View style={styles.inputContainer}>
            <MaterialCommunityIcons name="lock" size={20} color="#888" style={styles.inputIcon} />
            <TextInput
              style={styles.input}
              placeholder="Confirmar contraseña *"
              value={formData.confirmarContraseña}
              onChangeText={(value) => updateFormData("confirmarContraseña", value)}
              secureTextEntry
            />
          </View>

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

          <Text style={styles.requiredText}>* Campos obligatorios</Text>

          <TouchableOpacity style={styles.registerButton} onPress={handleRegister} disabled={loading}>
            {loading ? <ActivityIndicator color="#fff" /> : <Text style={styles.registerButtonText}>Registrarse</Text>}
          </TouchableOpacity>

          <Text style={styles.infoText}>Tu cuenta será revisada por un administrador antes de ser aprobada.</Text>

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
  scrollView: {
    flex: 1,
  },
  scrollContent: {
    flexGrow: 1,
  },
  overlay: {
    flex: 1,
    backgroundColor: "rgba(255, 255, 255, 0.9)",
    alignItems: "center",
    paddingTop: 60,
    paddingHorizontal: 20,
    paddingBottom: 40,
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
    minHeight: 50,
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
    paddingVertical: 15,
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
  requiredText: {
    color: "#666",
    fontSize: 12,
    marginBottom: 10,
    alignSelf: "flex-start",
    marginLeft: "5%",
  },
  registerButton: {
    backgroundColor: "#8B4513",
    paddingVertical: 15,
    borderRadius: 25,
    width: "90%",
    alignItems: "center",
    marginTop: 10,
    marginBottom: 15,
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
    paddingHorizontal: 20,
  },
  footerText: {
    color: "#666",
    fontSize: 14,
    marginTop: 10,
  },
})
