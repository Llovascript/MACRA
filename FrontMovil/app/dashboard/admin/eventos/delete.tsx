// DeleteEvento.tsx
"use client";

import { useState, useEffect } from "react";
import {
  View,
  Text,
  StyleSheet,
  Image,
  TouchableOpacity,
  ScrollView,
  Alert,
  ActivityIndicator,
  FlatList,
} from "react-native";
import { MaterialCommunityIcons } from "@expo/vector-icons";
import { useRouter } from "expo-router";
import AsyncStorage from "@react-native-async-storage/async-storage";
import { generateFastApiUrl } from "../../../../utils";

import FondoImage from "../../../../assets/images/fondo.jpg";

interface Evento {
  id: number;
  nombre: string;
  fechaIn: string;
  fechaTer: string;
  descripcion: string;
  estatus_id: number;
  del: number;   // 1 = activo, 0 = eliminado
}

export default function DeleteEvento() {
  const [eventos, setEventos] = useState<Evento[]>([]);
  const [selectedEvento, setSelectedEvento] = useState<Evento | null>(null);
  const [loading, setLoading] = useState(false);
  const [loadingEventos, setLoadingEventos] = useState(true);
  const router = useRouter();

  useEffect(() => {
    loadEventos();
  }, []);

  const loadEventos = async () => {
    try {
      setLoadingEventos(true);
      const token = await AsyncStorage.getItem("userToken");
      const apiUrl = generateFastApiUrl("/eventos/");

      const resp = await fetch(apiUrl, {
        headers: { Authorization: `Bearer ${token}` },
      });

      if (!resp.ok) {
        Alert.alert("Error", "No se pudieron cargar los eventos");
        return;
      }

      const data: Evento[] = await resp.json();
      // Mostrar solo los activos (del = 1)
      setEventos(data.filter(e => e.del === 1));
    } catch (err) {
      console.error("Error loading eventos:", err);
      Alert.alert("Error", "No se pudo conectar con el servidor");
    } finally {
      setLoadingEventos(false);
    }
  };

  const selectEvento = (evt: Evento) => {
    setSelectedEvento(evt);
  };

  const handleDelete = () => {
    if (!selectedEvento) {
      Alert.alert("Error", "Selecciona un evento para eliminar");
      return;
    }

    Alert.alert(
      "Confirmar eliminación",
      `¿Eliminar "${selectedEvento.nombre}"?`,
      [
        { text: "Cancelar", style: "cancel" },
        {
          text: "Eliminar",
          style: "destructive",
          onPress: async () => {
            setLoading(true);
            try {
              const token = await AsyncStorage.getItem("userToken");
              const apiUrl = generateFastApiUrl(`/eventos/${selectedEvento.id}`);

              const resp = await fetch(apiUrl, {
                method: "PUT",
                headers: {
                  "Content-Type": "application/json",
                  Authorization: `Bearer ${token}`,
                },
                body: JSON.stringify({
                  ...selectedEvento,
                  del: 0,   // marcamos como eliminado
                }),
              });

              if (!resp.ok) {
                const errorData = await resp.json();
                let msg = "No se pudo eliminar el evento";
                if (Array.isArray(errorData.detail)) {
                  msg = errorData.detail.map((e: any) => `${e.loc?.join(".")}: ${e.msg}`).join("\n");
                } else if (typeof errorData.detail === "string") {
                  msg = errorData.detail;
                }
                Alert.alert("Error", msg);
              } else {
                Alert.alert("Éxito", "Evento eliminado correctamente", [
                  {
                    text: "OK",
                    onPress: () => {
                      setSelectedEvento(null);
                      loadEventos();  // refrescar la lista
                    },
                  },
                ]);
              }
            } catch (err) {
              console.error("Error deleting evento:", err);
              Alert.alert("Error", "No se pudo conectar con el servidor");
            } finally {
              setLoading(false);
            }
          },
        },
      ],
      { cancelable: true }
    );
  };

  // Lista de selección
  if (!selectedEvento) {
    return (
      <View style={styles.containerWithBackground}>
        <Image source={FondoImage} style={styles.backgroundImage} resizeMode="cover" />
        <View style={styles.overlay}>
          <View style={styles.header}>
            <TouchableOpacity style={styles.backButton} onPress={() => router.back()}>
              <MaterialCommunityIcons name="arrow-left" size={24} color="#333" />
            </TouchableOpacity>
            <Text style={styles.title}>Eliminar evento</Text>
            <View style={{ width: 40 }} />
          </View>
          <View style={styles.listContainer}>
            <Text style={styles.subtitle}>Selecciona un evento:</Text>
            {loadingEventos ? (
              <ActivityIndicator size="large" color="#8B4513" style={{ marginTop: 50 }} />
            ) : (
              <FlatList
                data={eventos}
                keyExtractor={item => item.id.toString()}
                renderItem={({ item }) => (
                  <TouchableOpacity style={styles.eventoItem} onPress={() => selectEvento(item)}>
                    <Text style={styles.eventoNombre}>{item.nombre}</Text>
                    <Text style={styles.eventoFecha}>
                      {new Date(item.fechaIn).toLocaleDateString()} —{" "}
                      {new Date(item.fechaTer).toLocaleDateString()}
                    </Text>
                    <MaterialCommunityIcons name="chevron-right" size={20} color="#666" />
                  </TouchableOpacity>
                )}
                showsVerticalScrollIndicator={false}
              />
            )}
          </View>
        </View>
      </View>
    );
  }

  // Detalles y botón de eliminar
  return (
    <View style={styles.containerWithBackground}>
      <Image source={FondoImage} style={styles.backgroundImage} resizeMode="cover" />
      <ScrollView style={styles.scrollContainer}>
        <View style={styles.overlay}>
          <View style={styles.header}>
            <TouchableOpacity style={styles.backButton} onPress={() => setSelectedEvento(null)}>
              <MaterialCommunityIcons name="arrow-left" size={24} color="#333" />
            </TouchableOpacity>
            <Text style={styles.title}>Eliminar evento</Text>
            <View style={{ width: 40 }} />
          </View>
          <View style={styles.formContainer}>
            <Text style={styles.sectionTitle}>Detalles del Evento</Text>
            <View style={styles.form}>
              <View style={styles.inputContainer}>
                <Text style={styles.readOnlyText}>{selectedEvento.nombre}</Text>
              </View>
              <View style={styles.inputContainer}>
                <Text style={styles.readOnlyText}>
                  Inicio: {new Date(selectedEvento.fechaIn).toLocaleDateString()}
                </Text>
              </View>
              <View style={styles.inputContainer}>
                <Text style={styles.readOnlyText}>
                  Término: {new Date(selectedEvento.fechaTer).toLocaleDateString()}
                </Text>
              </View>
              <View style={[styles.inputContainer, styles.textAreaContainer]}>
                <Text style={styles.readOnlyText}>{selectedEvento.descripcion}</Text>
              </View>
              <TouchableOpacity
                style={styles.deleteButton}
                onPress={handleDelete}
                disabled={loading}
              >
                {loading ? (
                  <ActivityIndicator color="white" />
                ) : (
                  <Text style={styles.deleteButtonText}>Eliminar</Text>
                )}
              </TouchableOpacity>
            </View>
          </View>
        </View>
      </ScrollView>
    </View>
  );
}

const styles = StyleSheet.create({
  containerWithBackground: { flex: 1 },
  backgroundImage: {
    position: "absolute",
    top: 0,
    left: 0,
    bottom: 0,
    right: 0,
    width: "100%",
    height: "100%",
  },
  scrollContainer: { flex: 1 },
  overlay: {
    backgroundColor: "rgba(255,255,255,0.9)",
    paddingTop: 60,
    paddingBottom: 40,
    minHeight: "100%",
  },
  header: {
    flexDirection: "row",
    alignItems: "center",
    justifyContent: "space-between",
    paddingHorizontal: 20,
    marginBottom: 30,
  },
  backButton: {
    backgroundColor: "white",
    borderRadius: 20,
    padding: 8,
    shadowColor: "#000",
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 3,
    elevation: 3,
  },
  title: { fontSize: 24, fontWeight: "bold", color: "#333" },
  listContainer: { flex: 1, paddingHorizontal: 20 },
  subtitle: {
    fontSize: 18,
    fontWeight: "600",
    color: "#333",
    marginBottom: 20,
    textAlign: "center",
  },
  eventoItem: {
    backgroundColor: "white",
    borderRadius: 10,
    padding: 15,
    marginBottom: 10,
    flexDirection: "row",
    alignItems: "center",
    shadowColor: "#000",
    shadowOffset: { width: 0, height: 1 },
    shadowOpacity: 0.1,
    shadowRadius: 2,
    elevation: 2,
  },
  eventoNombre: { fontSize: 16, fontWeight: "600", color: "#333", flex: 1 },
  eventoFecha: { fontSize: 14, color: "#666", marginRight: 10 },
  formContainer: {
    backgroundColor: "white",
    margin: 20,
    borderRadius: 15,
    padding: 20,
    shadowColor: "#000",
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 3,
    elevation: 3,
  },
  sectionTitle: {
    fontSize: 18,
    fontWeight: "600",
    color: "#333",
    marginBottom: 20,
    textAlign: "center",
  },
  form: { gap: 15 },
  inputContainer: {
    backgroundColor: "#f5f5f5",
    borderRadius: 8,
    paddingHorizontal: 15,
    paddingVertical: 12,
    minHeight: 50,
    justifyContent: "center",
  },
  textAreaContainer: { paddingVertical: 15, minHeight: 100 },
  readOnlyText: { fontSize: 16, color: "#666" },
  deleteButton: {
    backgroundColor: "#f44336",
    paddingVertical: 15,
    borderRadius: 25,
    alignItems: "center",
    marginTop: 20,
  },
  deleteButtonText: { color: "white", fontSize: 18, fontWeight: "600" },
});
