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
  SafeAreaView,
} from "react-native"
import { Link, useRouter } from "expo-router"
import { MaterialCommunityIcons } from "@expo/vector-icons"
import AsyncStorage from "@react-native-async-storage/async-storage"
import { generateFastApiUrl } from "@/utils"

import FondoImage from "../assets/images/fondo.jpg"
import LogoImage from "../assets/images/logo.jpg"

export default function LoginScreen() {
  const [formData, setFormData] = useState({
    correo: "",
    contraseña: "",
  })
  const [loading, setLoading] = useState(false)
  const router = useRouter()

  const handleInputChange = (field: string, value: string) => {
    setFormData((prev) => ({
      ...prev,
      [field]: value,
    }))
  }

  const validateForm = () => {
    if (!formData.correo || !formData.contraseña) {
      Alert.alert("Error", "Por favor, completa todos los campos.")
      return false
    }

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
    if (!emailRegex.test(formData.correo)) {
      Alert.alert("Error", "Por favor, ingresa un correo electrónico válido.")
      return false
    }

    return true
  }

  const handleLogin = async () => {
    if (!validateForm()) return

    setLoading(true)
    try {
      const apiUrl = generateFastApiUrl("/auth/login")

      const response = await fetch(apiUrl, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({
          correo: formData.correo,
          contraseña: formData.contraseña,
        }),
      })

      const data = await response.json()

      if (response.ok) {
        // Guardar token
        await AsyncStorage.setItem("access_token", data.access_token)

        // Obtener información del usuario
        const userResponse = await fetch(generateFastApiUrl("/auth/me"), {
          headers: {
            Authorization: `Bearer ${data.access_token}`,
          },
        })

        if (userResponse.ok) {
          const userData = await userResponse.json()

          // Guardar datos del usuario
          await AsyncStorage.setItem("user_data", JSON.stringify(userData))
          await AsyncStorage.setItem("user_name", userData.nombre)
          await AsyncStorage.setItem("user_role", userData.rol_id.toString())

          // Redirigir según el rol
          switch (userData.rol_id) {
            case 1: // Admin
              router.replace("/dashboard/admin")
              break
            case 2: // Donante
              router.replace("/dashboard/donante")
              break
            case 3: // Beneficiario
              router.replace("/dashboard/beneficiario")
              break
            default:
              Alert.alert("Error", "Rol de usuario no reconocido.")
          }
        } else {
          Alert.alert("Error", "No se pudo obtener la información del usuario.")
        }
      } else {
        let errorMessage = "Credenciales incorrectas."

        if (data.detail === "User account not approved") {
          errorMessage = "Tu cuenta aún no ha sido aprobada por un administrador."
        } else if (data.detail === "Incorrect email or password") {
          errorMessage = "Correo electrónico o contraseña incorrectos."
        }

        Alert.alert("Error de inicio de sesión", errorMessage)
      }
    } catch (error: any) {
      Alert.alert("Error de conexión", "No se pudo conectar con el servidor.")
    } finally {
      setLoading(false)
    }
  }

  return (
    <SafeAreaView style={styles.container}>
      <Image source={FondoImage} style={styles.backgroundImage} resizeMode="cover" />
      <View style={styles.overlay}>
        <Link href="/" asChild>
          <TouchableOpacity style={styles.backButton}>
            <MaterialCommunityIcons name="arrow-left" size={24} color="#333" />
          </TouchableOpacity>
        </Link>

        <Image source={LogoImage} style={styles.logo} resizeMode="contain" />
        <Text style={styles.title}>Iniciar Sesión</Text>

        <View style={styles.inputContainer}>
          <MaterialCommunityIcons name="email" size={20} color="#888" style={styles.inputIcon} />
          <TextInput
            style={styles.input}
            placeholder="Correo electrónico"
            value={formData.correo}
            onChangeText={(value) => handleInputChange("correo", value)}
            keyboardType="email-address"
            autoCapitalize="none"
          />
        </View>

        <View style={styles.inputContainer}>
          <MaterialCommunityIcons name="lock" size={20} color="#888" style={styles.inputIcon} />
          <TextInput
            style={styles.input}
            placeholder="Contraseña"
            value={formData.contraseña}
            onChangeText={(value) => handleInputChange("contraseña", value)}
            secureTextEntry
          />
        </View>

        <TouchableOpacity style={styles.loginButton} onPress={handleLogin} disabled={loading}>
          {loading ? <ActivityIndicator color="#fff" /> : <Text style={styles.loginButtonText}>Iniciar Sesión</Text>}
        </TouchableOpacity>

        <View style={styles.registerContainer}>
          <Text style={styles.registerText}>¿No tienes cuenta? </Text>
          <Link href="/register" asChild>
            <TouchableOpacity>
              <Text style={styles.registerLink}>Regístrate aquí</Text>
            </TouchableOpacity>
          </Link>
        </View>

        <Text style={styles.footerText}>© 2025 MACRA Banco de Alimentos</Text>
      </View>
    </SafeAreaView>
  )
}

const styles = StyleSheet.create({
  container: {
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
    backgroundColor: "rgba(255, 255, 255, 0.9)",
    justifyContent: "center",
    alignItems: "center",
    paddingHorizontal: 20,
  },
  backButton: {
    position: "absolute",
    top: 60,
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
    fontSize: 28,
    fontWeight: "bold",
    color: "#333",
    marginBottom: 40,
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
  loginButton: {
    backgroundColor: "#8B4513",
    paddingVertical: 15,
    borderRadius: 25,
    width: "90%",
    alignItems: "center",
    marginTop: 20,
    marginBottom: 30,
  },
  loginButtonText: {
    color: "white",
    fontSize: 18,
    fontWeight: "bold",
  },
  registerContainer: {
    flexDirection: "row",
    alignItems: "center",
    marginBottom: 30,
  },
  registerText: {
    color: "#666",
    fontSize: 16,
  },
  registerLink: {
    color: "#8B4513",
    fontSize: 16,
    fontWeight: "bold",
  },
  footerText: {
    color: "#666",
    fontSize: 14,
    position: "absolute",
    bottom: 30,
  },
})
