"use client"

import { useState, useEffect } from "react"
import { View, Text, StyleSheet, Image, TouchableOpacity, SafeAreaView, Alert } from "react-native"
import { useRouter } from "expo-router"
import { MaterialCommunityIcons } from "@expo/vector-icons"
import AsyncStorage from "@react-native-async-storage/async-storage"

import FondoImage from "../../assets/images/fondo.jpg"

export default function AdminDashboard() {
  const [userName, setUserName] = useState("")
  const router = useRouter()

  useEffect(() => {
    loadUserData()
  }, [])

  const loadUserData = async () => {
    try {
      const name = await AsyncStorage.getItem("user_name")
      if (name) {
        setUserName(name)
      }
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
            await AsyncStorage.multiRemove(["access_token", "user_data", "user_name", "user_role"])
            router.replace("/")
          } catch (error) {
            console.error("Error during logout:", error)
          }
        },
      },
    ])
  }

  const menuItems = [
    {
      title: "Solicitudes",
      icon: "clipboard-list",
      color: "#4CAF50",
      onPress: () => router.push("/admin/solicitudes"),
    },
    {
      title: "Eventos",
      icon: "calendar-star",
      color: "#FF5722",
      onPress: () => router.push("/admin/eventos"),
    },
    {
      title: "Donantes",
      icon: "hand-heart",
      color: "#E91E63",
      onPress: () => router.push("/admin/donantes"),
    },
    {
      title: "Beneficiarios",
      icon: "account-group",
      color: "#2196F3",
      onPress: () => router.push("/admin/beneficiarios"),
    },
  ]

  return (
    <SafeAreaView style={styles.container}>
      <Image source={FondoImage} style={styles.backgroundImage} resizeMode="cover" />
      <View style={styles.overlay}>
        <TouchableOpacity style={styles.logoutButton} onPress={handleLogout}>
          <MaterialCommunityIcons name="logout" size={24} color="#fff" />
        </TouchableOpacity>

        <Text style={styles.welcomeText}>¡Bienvenido{"\n"}Administrador!</Text>
        {userName ? <Text style={styles.nameText}>{userName}</Text> : null}

        <View style={styles.menuContainer}>
          {menuItems.map((item, index) => (
            <TouchableOpacity key={index} style={styles.menuItem} onPress={item.onPress}>
              <View style={[styles.iconContainer, { backgroundColor: item.color }]}>
                <MaterialCommunityIcons name={item.icon as any} size={32} color="#fff" />
              </View>
              <Text style={styles.menuText}>{item.title}</Text>
            </TouchableOpacity>
          ))}
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
    fontSize: 28,
    fontWeight: "bold",
    color: "#fff",
    textAlign: "left",
    marginTop: 40,
    marginBottom: 10,
    textShadowColor: "rgba(0, 0, 0, 0.7)",
    textShadowOffset: { width: 1, height: 1 },
    textShadowRadius: 3,
  },
  nameText: {
    fontSize: 18,
    color: "#fff",
    textAlign: "left",
    marginBottom: 40,
    textShadowColor: "rgba(0, 0, 0, 0.7)",
    textShadowOffset: { width: 1, height: 1 },
    textShadowRadius: 3,
  },
  menuContainer: {
    flex: 1,
    justifyContent: "center",
  },
  menuItem: {
    flexDirection: "row",
    alignItems: "center",
    backgroundColor: "rgba(255, 255, 255, 0.9)",
    borderRadius: 15,
    padding: 20,
    marginBottom: 15,
    shadowColor: "#000",
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.25,
    shadowRadius: 3.84,
    elevation: 5,
  },
  iconContainer: {
    width: 60,
    height: 60,
    borderRadius: 15,
    justifyContent: "center",
    alignItems: "center",
    marginRight: 20,
  },
  menuText: {
    fontSize: 18,
    fontWeight: "600",
    color: "#333",
    flex: 1,
  },
})
