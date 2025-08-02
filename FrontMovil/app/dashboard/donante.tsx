"use client"

import { useState, useEffect } from "react"
import { View, Text, StyleSheet, Image, TouchableOpacity, Alert, SafeAreaView } from "react-native"
import { useRouter } from "expo-router"
import { MaterialCommunityIcons } from "@expo/vector-icons"
import AsyncStorage from "@react-native-async-storage/async-storage"

import FondoImage from "../../assets/images/fondo.jpg"

export default function DonanteDashboard() {
  const [userName, setUserName] = useState("")
  const router = useRouter()

  useEffect(() => {
    loadUserData()
  }, [])

  const loadUserData = async () => {
    try {
      const name = await AsyncStorage.getItem("user_name")
      setUserName(name || "Donador")
    } catch (error) {
      console.error("Error loading user data:", error)
    }
  }

  const handleLogout = async () => {
    Alert.alert("Cerrar Sesión", "¿Estás seguro de que deseas cerrar sesión?", [
      { text: "Cancelar", style: "cancel" },
      {
        text: "Cerrar Sesión",
        style: "destructive",
        onPress: async () => {
          try {
            await AsyncStorage.multiRemove(["access_token", "user_role", "user_name", "user_id"])
            router.replace("/")
          } catch (error) {
            console.error("Error during logout:", error)
          }
        },
      },
    ])
  }

  const handleMenuOption = (option: string) => {
    Alert.alert("Próximamente", `La función ${option} estará disponible pronto.`)
  }

  return (
    <SafeAreaView style={styles.container}>
      <Image source={FondoImage} style={styles.backgroundImage} resizeMode="cover" />
      <View style={styles.overlay}>
        <TouchableOpacity style={styles.logoutButton} onPress={handleLogout}>
          <MaterialCommunityIcons name="logout" size={24} color="#fff" />
        </TouchableOpacity>

        <Text style={styles.welcomeText}>¡Bienvenido</Text>
        <Text style={styles.welcomeText}>Donador!</Text>

        <View style={styles.menuContainer}>
          <View style={styles.menuRow}>
            <TouchableOpacity style={styles.menuCard} onPress={() => handleMenuOption("Perfil")}>
              <View style={styles.iconContainer}>
                <MaterialCommunityIcons name="account-circle" size={40} color="#2196F3" />
              </View>
              <Text style={styles.menuText}>Perfil</Text>
            </TouchableOpacity>

            <TouchableOpacity style={styles.menuCard} onPress={() => handleMenuOption("Donaciones Realizadas")}>
              <View style={styles.iconContainer}>
                <MaterialCommunityIcons name="heart-multiple" size={40} color="#E91E63" />
              </View>
              <Text style={styles.menuText}>Donaciones{"\n"}Realizadas</Text>
            </TouchableOpacity>
          </View>

          <View style={styles.menuRow}>
            <TouchableOpacity style={styles.menuCard} onPress={() => handleMenuOption("Eventos")}>
              <View style={styles.iconContainer}>
                <MaterialCommunityIcons name="calendar-star" size={40} color="#FF5722" />
              </View>
              <Text style={styles.menuText}>Eventos</Text>
            </TouchableOpacity>

            <TouchableOpacity style={styles.menuCard} onPress={() => handleMenuOption("Estatus Solicitudes")}>
              <View style={styles.iconContainer}>
                <MaterialCommunityIcons name="clipboard-check" size={40} color="#00BCD4" />
              </View>
              <Text style={styles.menuText}>Estatus{"\n"}Solicitudes</Text>
            </TouchableOpacity>
          </View>
        </View>
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
    backgroundColor: "rgba(0, 0, 0, 0.4)",
    paddingHorizontal: 20,
    paddingTop: 60,
  },
  logoutButton: {
    position: "absolute",
    top: 60,
    right: 20,
    backgroundColor: "rgba(139, 69, 19, 0.8)",
    borderRadius: 20,
    padding: 10,
    zIndex: 1,
  },
  welcomeText: {
    fontSize: 32,
    fontWeight: "bold",
    color: "#fff",
    textAlign: "left",
    textShadowColor: "rgba(0, 0, 0, 0.7)",
    textShadowOffset: { width: 1, height: 1 },
    textShadowRadius: 3,
    marginTop: 20,
  },
  menuContainer: {
    flex: 1,
    justifyContent: "center",
    paddingVertical: 40,
  },
  menuRow: {
    flexDirection: "row",
    justifyContent: "space-between",
    marginVertical: 10,
  },
  menuCard: {
    backgroundColor: "rgba(255, 255, 255, 0.9)",
    borderRadius: 15,
    padding: 15,
    width: "47%",
    alignItems: "center",
    shadowColor: "#000",
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.3,
    shadowRadius: 5,
    elevation: 8,
    minHeight: 120,
    justifyContent: "center",
  },
  iconContainer: {
    marginBottom: 10,
  },
  menuText: {
    fontSize: 16,
    fontWeight: "600",
    color: "#333",
    textAlign: "center",
    lineHeight: 18,
  },
})
