"use client"

import { useState } from "react"
import {
  View,
  Text,
  StyleSheet,
  Image, // Eliminamos ImageBackground
  TextInput,
  TouchableOpacity,
  Alert,
  ActivityIndicator,
} from "react-native"
import { Link, useRouter } from "expo-router"
import { MaterialCommunityIcons } from "@expo/vector-icons"
import { Picker } from "@react-native-picker/picker"
import { generateFastApiUrl } from "@/utils" 

import FondoImage from "../assets/images/fondo.jpg" // Importa la imagen de fondo
import LogoImage from "../assets/images/logo.jpg"

const ROLES = [
  { label: "Seleccionar perfil", value: "" },
  { label: "Usuario", value: "2" },
  { label: "Donante", value: "4" },
  { label: "Beneficiario", value: "5" },
]

export default function RegisterScreen() {
  const [email, setEmail] = useState("")
  const [password, setPassword] = useState("")
  const [confirmPassword, setConfirmPassword] = useState("")
  const [selectedRole, setSelectedRole] = useState("")
  const [loading, setLoading] = useState(false)
  const router = useRouter()

  const handleRegister = async () => {
    if (!email || !password || !confirmPassword || !selectedRole) {
      Alert.alert("Error", "Por favor, completa todos los campos y selecciona un perfil.")
      return
    }
    if (password !== confirmPassword) {
      Alert.alert("Error", "Las contraseñas no coinciden.")
      return
    }

    setLoading(true)
    try {
      const apiUrl = generateFastApiUrl("/auth/register")
      console.log("Attempting registration to:", apiUrl)

      const response = await fetch(apiUrl, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({
          correo: email,
          contraseña: password,
          rol_id: Number.parseInt(selectedRole),
        }),
      })

      const data = await response.json()

      if (response.ok) {
        Alert.alert("Éxito", data.message || "Registro exitoso! Ahora puedes iniciar sesión.")
        router.replace("/login")
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
      <View style={styles.overlay}>
        <Link href="/" asChild>
          <TouchableOpacity style={styles.backButton}>
            <MaterialCommunityIcons name="arrow-left" size={24} color="#333" />
          </TouchableOpacity>
        </Link>

        <Image source={LogoImage} style={styles.logo} resizeMode="contain" />

        <Text style={styles.title}>Crear nueva cuenta</Text>

        <View style={styles.inputContainer}>
          <MaterialCommunityIcons name="email" size={20} color="#888" style={styles.inputIcon} />
          <TextInput
            style={styles.input}
            placeholder="Correo electrónico"
            value={email}
            onChangeText={setEmail}
            keyboardType="email-address"
            autoCapitalize="none"
          />
        </View>

        <View style={styles.inputContainer}>
          <MaterialCommunityIcons name="lock" size={20} color="#888" style={styles.inputIcon} />
          <TextInput
            style={styles.input}
            placeholder="Contraseña"
            value={password}
            onChangeText={setPassword}
            secureTextEntry
          />
        </View>

        <View style={styles.inputContainer}>
          <MaterialCommunityIcons name="lock" size={20} color="#888" style={styles.inputIcon} />
          <TextInput
            style={styles.input}
            placeholder="Confirmar contraseña"
            value={confirmPassword}
            onChangeText={setConfirmPassword}
            secureTextEntry
          />
        </View>

        <View style={styles.pickerContainer}>
          <Picker
            selectedValue={selectedRole}
            onValueChange={(itemValue: string) => setSelectedRole(itemValue)}
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
          {loading ? <ActivityIndicator color="#fff" /> : <Text style={styles.registerButtonText}>Registrarse</Text>}
        </TouchableOpacity>

        <Text style={styles.footerText}>© 2025 MACRA Banco de Alimentos</Text>
      </View>
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
  overlay: {
    flex: 1,
    backgroundColor: "rgba(255, 255, 255, 0.8)",
    alignItems: "center",
    paddingTop: 60,
    paddingHorizontal: 20,
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
    width: 120,
    height: 120,
    marginBottom: 30,
  },
  title: {
    fontSize: 26,
    fontWeight: "bold",
    color: "#333",
    marginBottom: 30,
  },
  inputContainer: {
    flexDirection: "row",
    alignItems: "center",
    backgroundColor: "white",
    borderRadius: 25,
    paddingHorizontal: 15,
    marginBottom: 20,
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
    marginBottom: 20,
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
  footerText: {
    position: "absolute",
    bottom: 20,
    color: "#666",
    fontSize: 14,
  },
})
