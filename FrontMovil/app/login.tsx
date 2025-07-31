"use client"

import { useState } from "react"
import { View, Text, StyleSheet, Image, TextInput, TouchableOpacity, Alert, ActivityIndicator } from "react-native"
import { Link, useRouter } from "expo-router"
import { MaterialCommunityIcons } from "@expo/vector-icons"
import { generateFastApiUrl } from "../utils"
import AsyncStorage from "@react-native-async-storage/async-storage"

import FondoImage from "../assets/images/fondo.jpg"
import LogoImage from "../assets/images/logo.jpg"

export default function LoginScreen() {
  const [email, setEmail] = useState("")
  const [password, setPassword] = useState("")
  const [loading, setLoading] = useState(false)
  const router = useRouter()

  const handleLogin = async () => {
    if (!email || !password) {
      Alert.alert("Error", "Por favor, ingresa tu correo electrónico y contraseña.")
      return
    }

    setLoading(true)
    try {
      const apiUrl = generateFastApiUrl("/auth/login")
      console.log("Attempting login to:", apiUrl)

      const response = await fetch(apiUrl, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({ correo: email, contraseña: password }),
      })

      const data = await response.json()

      if (response.ok) {
        // Guardar el token
        await AsyncStorage.setItem("userToken", data.access_token)

        // Obtener información del usuario para determinar su rol
        const userInfoUrl = generateFastApiUrl("/auth/me")
        const userResponse = await fetch(userInfoUrl, {
          headers: {
            Authorization: `Bearer ${data.access_token}`,
          },
        })

        if (userResponse.ok) {
          const userData = await userResponse.json()

          // Verificar si el usuario está aprobado
          if (!userData.aprobacion) {
            Alert.alert(
              "Cuenta Pendiente de Aprobación",
              "Tu cuenta aún está siendo revisada por un administrador. Te notificaremos cuando sea aprobada.",
              [
                {
                  text: "Entendido",
                  onPress: async () => {
                    // Limpiar token ya que no puede acceder
                    await AsyncStorage.removeItem("userToken")
                    router.replace("/")
                  },
                },
              ],
            )
            return
          }

          // Guardar información del usuario
          await AsyncStorage.setItem("userData", JSON.stringify(userData))

          Alert.alert("Éxito", "Inicio de sesión exitoso!")

          // Redirigir al dashboard correspondiente según el rol
          switch (userData.rol_id) {
            case 1: // admin
              router.replace("/dashboard/admin")
              break
            case 4: // donante
              router.replace("/dashboard/donante")
              break
            case 5: // beneficiario
              router.replace("/dashboard/beneficiario")
              break
            default:
              router.replace("/dashboard/usuario") // fallback
              break
          }
        } else {
          Alert.alert("Error", "No se pudo obtener la información del usuario.")
        }
      } else {
        Alert.alert("Error de inicio de sesión", data.detail || "Credenciales incorrectas.")
      }
    } catch (error: any) {
      console.error("Error al iniciar sesión:", error)
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

        <Text style={styles.title}>¡Bienvenido de nuevo!</Text>

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

        <TouchableOpacity style={styles.loginButton} onPress={handleLogin} disabled={loading}>
          {loading ? <ActivityIndicator color="#fff" /> : <Text style={styles.loginButtonText}>Iniciar Sesión</Text>}
        </TouchableOpacity>

        <TouchableOpacity>
          <Text style={styles.forgotPasswordText}>¿Olvidaste tu contraseña?</Text>
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
  loginButton: {
    backgroundColor: "#8B4513",
    paddingVertical: 15,
    borderRadius: 25,
    width: "90%",
    alignItems: "center",
    marginTop: 10,
    marginBottom: 20,
  },
  loginButtonText: {
    color: "white",
    fontSize: 18,
    fontWeight: "bold",
  },
  forgotPasswordText: {
    color: "#666",
    fontSize: 15,
    marginBottom: 20,
  },
  footerText: {
    position: "absolute",
    bottom: 20,
    color: "#666",
    fontSize: 14,
  },
})
