"use client"

import { useState, useEffect } from "react"
import { View, Text, StyleSheet, Image, TouchableOpacity, ScrollView, Alert } from "react-native"
import { MaterialCommunityIcons } from "@expo/vector-icons"
import { useRouter } from "expo-router"
import AsyncStorage from "@react-native-async-storage/async-storage"

import FondoImage from "../../assets/images/fondo.jpg"

interface User {
  id: number
  correo: string
  rol_id: number
  // Agrega más campos según tu API
}

export default function AdminDashboard() {
  const [user, setUser] = useState<User | null>(null)
  const router = useRouter()

  useEffect(() => {
    loadUserData()
  }, [])

  const loadUserData = async () => {
    try {
      const userData = await AsyncStorage.getItem("userData")
      if (userData) {
        setUser(JSON.parse(userData))
      }
    } catch (error) {
      console.error("Error loading user data:", error)
    }
  }

  const handleLogout = async () => {
    Alert.alert("Cerrar Sesión", "¿Estás seguro de que quieres cerrar sesión?", [
      { text: "Cancelar", style: "cancel" },
      {
        text: "Cerrar Sesión",
        onPress: async () => {
          await AsyncStorage.removeItem("userToken")
          await AsyncStorage.removeItem("userData")
          router.replace("/")
        },
      },
    ])
  }

  const menuItems = [
    {
      title: "Solicitudes",
      icon: "clipboard-list",
      onPress: () => Alert.alert("Solicitudes", "Funcionalidad en desarrollo"),
    },
    {
      title: "Eventos",
      icon: "calendar",
      onPress: () => Alert.alert("Eventos", "Funcionalidad en desarrollo"),
    },
    {
      title: "Donantes",
      icon: "heart-outline",
      onPress: () => Alert.alert("Donantes", "Funcionalidad en desarrollo"),
    },
    {
      title: "Beneficiarios",
      icon: "account-group",
      onPress: () => Alert.alert("Beneficiarios", "Funcionalidad en desarrollo"),
    },
  ]

  return (
    <View style={styles.containerWithBackground}>
      <Image source={FondoImage} style={styles.backgroundImage} resizeMode="cover" />
      <View style={styles.overlay}>
        <View style={styles.header}>
          <TouchableOpacity style={styles.logoutButton} onPress={handleLogout}>
            <MaterialCommunityIcons name="logout" size={24} color="#333" />
          </TouchableOpacity>
          <Text style={styles.welcomeText}>¡Bienvenido Administrador!</Text>
          <Text style={styles.emailText}>{user?.correo}</Text>
        </View>

        <ScrollView style={styles.menuContainer} showsVerticalScrollIndicator={false}>
          <View style={styles.menuGrid}>
            {menuItems.map((item, index) => (
              <TouchableOpacity key={index} style={styles.menuItem} onPress={item.onPress}>
                <View style={styles.iconContainer}>
                  <MaterialCommunityIcons name={item.icon as any} size={40} color="#8B4513" />
                </View>
                <Text style={styles.menuItemText}>{item.title}</Text>
              </TouchableOpacity>
            ))}
          </View>
        </ScrollView>
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
    backgroundColor: "rgba(255, 255, 255, 0.9)",
    paddingTop: 60,
    paddingHorizontal: 20,
  },
  header: {
    alignItems: "center",
    marginBottom: 30,
  },
  logoutButton: {
    position: "absolute",
    top: -20,
    right: 0,
    backgroundColor: "white",
    borderRadius: 20,
    padding: 8,
    shadowColor: "#000",
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 3,
    elevation: 3,
  },
  welcomeText: {
    fontSize: 28,
    fontWeight: "bold",
    color: "#333",
    marginBottom: 10,
  },
  emailText: {
    fontSize: 16,
    color: "#666",
  },
  menuContainer: {
    flex: 1,
  },
  menuGrid: {
    flexDirection: "row",
    flexWrap: "wrap",
    justifyContent: "space-between",
  },
  menuItem: {
    width: "48%",
    backgroundColor: "white",
    borderRadius: 15,
    padding: 20,
    marginBottom: 15,
    alignItems: "center",
    shadowColor: "#000",
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 3,
    elevation: 3,
  },
  iconContainer: {
    marginBottom: 10,
  },
  menuItemText: {
    fontSize: 16,
    fontWeight: "600",
    color: "#333",
    textAlign: "center",
  },
})
