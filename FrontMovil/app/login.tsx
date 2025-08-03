"use client"

import { useState } from "react"
import {
  View,
  Text,
  TextInput,
  TouchableOpacity,
  StyleSheet,
  Alert,
  ActivityIndicator,
  ImageBackground,
  Image,
  KeyboardAvoidingView,
  Platform,
  ScrollView,
} from "react-native"
import { useRouter } from "expo-router"
import AsyncStorage from "@react-native-async-storage/async-storage"
import { generateFastApiUrl } from "@/utils"

export default function LoginScreen() {
  const [email, setEmail] = useState("")
  const [password, setPassword] = useState("")
  const [loading, setLoading] = useState(false)
  const router = useRouter()

  const handleLogin = async () => {
    if (!email || !password) {
      Alert.alert("Error", "Por favor ingresa email y contraseña")
      return
    }

    setLoading(true)
    try {
      console.log("=== INICIANDO LOGIN ===")
      console.log("Email:", email)
      console.log("URL:", generateFastApiUrl("/auth/login"))

      const loginData = {
        correo: email,
        contraseña: password,
      }

      console.log("Login data:", loginData)

      const response = await fetch(generateFastApiUrl("/auth/login"), {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(loginData),
      })

      console.log("Response status:", response.status)

      if (response.ok) {
        const data = await response.json()
        console.log("Login response:", data)

        // Guardar token
        await AsyncStorage.setItem("token", data.access_token)
        console.log("Token guardado exitosamente")

        // Verificar que se guardó
        const savedToken = await AsyncStorage.getItem("token")
        console.log("Token verificado:", savedToken ? "✅ Existe" : "❌ No existe")

        // Obtener datos del usuario
        const userResponse = await fetch(generateFastApiUrl("/auth/me"), {
          headers: {
            Authorization: `Bearer ${data.access_token}`,
          },
        })

        if (userResponse.ok) {
          const userData = await userResponse.json()
          console.log("User data:", userData)

          // Guardar datos del usuario
          await AsyncStorage.setItem("user", JSON.stringify(userData))

          // Redirigir según el rol
          if (userData.rol_id === 1) {
            router.replace("/dashboard/admin")
          } else if (userData.rol_id === 2) {
            router.replace("/dashboard/donante")
          } else if (userData.rol_id === 3) {
            router.replace("/dashboard/beneficiario")
          } else {
            router.replace("/dashboard/admin") // Por defecto
          }
        } else {
          Alert.alert("Error", "No se pudieron obtener los datos del usuario")
        }
      } else {
        const errorData = await response.text()
        console.error("Error response:", errorData)
        Alert.alert("Error", "Credenciales incorrectas")
      }
    } catch (error) {
      console.error("Login error:", error)
      Alert.alert(
        "Error de conexión",
        "No se pudo conectar con el servidor. Verifica:\n1. Que el servidor FastAPI esté ejecutándose\n2. Tu conexión a internet\n3. La dirección IP en utils.ts",
      )
    } finally {
      setLoading(false)
    }
  }

  return (
    <ImageBackground source={require("@/assets/images/fondo.jpg")} style={styles.background}>
      <KeyboardAvoidingView style={styles.container} behavior={Platform.OS === "ios" ? "padding" : "height"}>
        <ScrollView contentContainerStyle={styles.scrollContainer} keyboardShouldPersistTaps="handled">
          <View style={styles.logoContainer}>
            <Image source={require("@/assets/images/logo.jpg")} style={styles.logo} />
            <Text style={styles.title}>MACRA</Text>
            <Text style={styles.subtitle}>Manejo de Alimentos y Caridad Responsable</Text>
          </View>

          <View style={styles.formContainer}>
            <View style={styles.inputContainer}>
              <TextInput
                style={styles.input}
                placeholder="Correo electrónico"
                placeholderTextColor="#999"
                value={email}
                onChangeText={setEmail}
                keyboardType="email-address"
                autoCapitalize="none"
                autoCorrect={false}
              />
            </View>

            <View style={styles.inputContainer}>
              <TextInput
                style={styles.input}
                placeholder="Contraseña"
                placeholderTextColor="#999"
                value={password}
                onChangeText={setPassword}
                secureTextEntry
                autoCapitalize="none"
                autoCorrect={false}
              />
            </View>

            <TouchableOpacity style={styles.loginButton} onPress={handleLogin} disabled={loading}>
              {loading ? (
                <ActivityIndicator color="#fff" />
              ) : (
                <Text style={styles.loginButtonText}>Iniciar Sesión</Text>
              )}
            </TouchableOpacity>

            <TouchableOpacity style={styles.registerLink} onPress={() => router.push("/register")}>
              <Text style={styles.registerText}>¿No tienes cuenta? Regístrate aquí</Text>
            </TouchableOpacity>
          </View>
        </ScrollView>
      </KeyboardAvoidingView>
    </ImageBackground>
  )
}

const styles = StyleSheet.create({
  background: {
    flex: 1,
    resizeMode: "cover",
  },
  container: {
    flex: 1,
  },
  scrollContainer: {
    flexGrow: 1,
    justifyContent: "center",
    padding: 20,
  },
  logoContainer: {
    alignItems: "center",
    marginBottom: 40,
  },
  logo: {
    width: 120,
    height: 120,
    borderRadius: 60,
    marginBottom: 20,
  },
  title: {
    fontSize: 32,
    fontWeight: "bold",
    color: "#8B4513",
    marginBottom: 8,
    textShadowColor: "rgba(255, 255, 255, 0.8)",
    textShadowOffset: { width: 1, height: 1 },
    textShadowRadius: 2,
  },
  subtitle: {
    fontSize: 16,
    color: "#5D4E37",
    textAlign: "center",
    fontWeight: "500",
    textShadowColor: "rgba(255, 255, 255, 0.8)",
    textShadowOffset: { width: 1, height: 1 },
    textShadowRadius: 2,
  },
  formContainer: {
    backgroundColor: "rgba(255, 255, 255, 0.95)",
    borderRadius: 20,
    padding: 30,
    shadowColor: "#000",
    shadowOffset: {
      width: 0,
      height: 4,
    },
    shadowOpacity: 0.3,
    shadowRadius: 4.65,
    elevation: 8,
  },
  inputContainer: {
    marginBottom: 20,
  },
  input: {
    backgroundColor: "#fff",
    borderWidth: 1,
    borderColor: "#ddd",
    borderRadius: 12,
    paddingHorizontal: 16,
    paddingVertical: 14,
    fontSize: 16,
    color: "#333",
    shadowColor: "#000",
    shadowOffset: {
      width: 0,
      height: 1,
    },
    shadowOpacity: 0.1,
    shadowRadius: 2,
    elevation: 2,
  },
  loginButton: {
    backgroundColor: "#8B4513",
    borderRadius: 12,
    paddingVertical: 16,
    alignItems: "center",
    marginTop: 10,
    shadowColor: "#000",
    shadowOffset: {
      width: 0,
      height: 2,
    },
    shadowOpacity: 0.25,
    shadowRadius: 3.84,
    elevation: 5,
  },
  loginButtonText: {
    color: "#fff",
    fontSize: 18,
    fontWeight: "bold",
  },
  registerLink: {
    marginTop: 20,
    alignItems: "center",
  },
  registerText: {
    color: "#8B4513",
    fontSize: 16,
    fontWeight: "500",
  },
})
