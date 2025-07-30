import React from 'react';
import {
  View,
  Text,
  TouchableOpacity,
  StyleSheet,
  SafeAreaView,
  ImageBackground,
} from 'react-native';
import Icon from 'react-native-vector-icons/MaterialIcons';
import FontAwesome from 'react-native-vector-icons/FontAwesome';

const MenuBeneficiario = ({ navigation }) => {
  const handleLogout = () => {
    // Lógica para cerrar sesión
    console.log('Cerrando sesión...');
  };

  const navigateToPerfil = () => {
    // Navegar a la pantalla de perfil
    console.log('Navegando a Perfil...');
    // navigation.navigate('Perfil');
  };

  const navigateToEventos = () => {
    // Navegar a la pantalla de eventos
    console.log('Navegando a Eventos...');
    // navigation.navigate('Eventos');
  };

  const navigateToEntregas = () => {
    // Navegar a la pantalla de entregas
    console.log('Navegando a Entregas...');
    // navigation.navigate('Entregas');
  };

  return (
    <SafeAreaView style={styles.container}>
      <ImageBackground
        source={{
          uri: 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAwIiBoZWlnaHQ9IjgwMCIgdmlld0JveD0iMCAwIDQwMCA4MDAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxkZWZzPgo8bGluZWFyR3JhZGllbnQgaWQ9InBhaW50MF9saW5lYXJfMF8xIiB4MT0iMCIgeTE9IjAiIHgyPSI0MDAiIHkyPSI4MDAiIGdyYWRpZW50VW5pdHM9InVzZXJTcGFjZU9uVXNlIj4KPHN0b3Agc3RvcC1jb2xvcj0iI0ZGRjJGOCIvPgo8c3RvcCBvZmZzZXQ9IjAuNSIgc3RvcC1jb2xvcj0iI0Y4RjlGQSIvPgo8c3RvcCBvZmZzZXQ9IjEiIHN0b3AtY29sb3I9IiNGM0Y0RjYiLz4KPC9saW5lYXJHcmFkaWVudD4KPC9kZWZzPgo8cmVjdCB3aWR0aD0iNDAwIiBoZWlnaHQ9IjgwMCIgZmlsbD0idXJsKCNwYWludDBfbGluZWFyXzBfMSkiLz4KPC9zdmc+'
        }}
        style={styles.backgroundImage}
        resizeMode="cover"
      >
        <View style={styles.overlay}>
          {/* Header */}
          <View style={styles.header}>
            <Text style={styles.headerTitle}>Menú Beneficiario</Text>
            <TouchableOpacity style={styles.logoutButton} onPress={handleLogout}>
              <Icon name="exit-to-app" size={24} color="#666" />
            </TouchableOpacity>
          </View>

          {/* Welcome Message */}
          <View style={styles.welcomeContainer}>
            <Text style={styles.welcomeText}>¡Bienvenido</Text>
            <Text style={styles.welcomeText}>Beneficiario!</Text>
          </View>

          {/* Menu Options */}
          <View style={styles.menuContainer}>
            {/* Perfil Card */}
            <TouchableOpacity style={styles.menuCard} onPress={navigateToPerfil}>
              <View style={styles.cardContent}>
                <View style={styles.iconContainer}>
                  <View style={styles.profileIcon}>
                    <Icon name="person" size={30} color="#4A90E2" />
                    <View style={styles.idBadge}>
                      <View style={styles.redSquare} />
                    </View>
                  </View>
                </View>
                <Text style={styles.cardText}>Perfil</Text>
              </View>
            </TouchableOpacity>

            {/* Eventos Card */}
            <TouchableOpacity style={styles.menuCard} onPress={navigateToEventos}>
              <View style={styles.cardContent}>
                <View style={styles.iconContainer}>
                  <FontAwesome name="calendar" size={32} color="#E74C3C" />
                </View>
                <Text style={styles.cardText}>Eventos</Text>
              </View>
            </TouchableOpacity>

            {/* Entregas Card */}
            <TouchableOpacity style={styles.menuCard} onPress={navigateToEntregas}>
              <View style={styles.cardContent}>
                <View style={styles.iconContainer}>
                  <View style={styles.heartHandIcon}>
                    <FontAwesome name="heart" size={28} color="#E74C3C" />
                    <View style={styles.handIcon}>
                      <FontAwesome name="hand-paper-o" size={20} color="#4A90E2" />
                    </View>
                  </View>
                </View>
                <Text style={styles.cardText}>Entregas</Text>
              </View>
            </TouchableOpacity>
          </View>
        </View>
      </ImageBackground>
    </SafeAreaView>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
  },
  backgroundImage: {
    flex: 1,
  },
  overlay: {
    flex: 1,
    backgroundColor: 'rgba(255, 255, 255, 0.1)',
  },
  header: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingHorizontal: 20,
    paddingTop: 20,
    paddingBottom: 10,
  },
  headerTitle: {
    fontSize: 16,
    color: '#666',
    fontWeight: '400',
  },
  logoutButton: {
    padding: 8,
  },
  welcomeContainer: {
    paddingHorizontal: 20,
    paddingVertical: 30,
  },
  welcomeText: {
    fontSize: 28,
    fontWeight: 'bold',
    color: '#333',
    lineHeight: 34,
  },
  menuContainer: {
    flex: 1,
    paddingHorizontal: 20,
    paddingTop: 20,
  },
  menuCard: {
    backgroundColor: 'rgba(255, 255, 255, 0.95)',
    borderRadius: 12,
    marginBottom: 16,
    shadowColor: '#000',
    shadowOffset: {
      width: 0,
      height: 2,
    },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 3,
  },
  cardContent: {
    flexDirection: 'row',
    alignItems: 'center',
    padding: 20,
  },
  iconContainer: {
    marginRight: 20,
    width: 50,
    height: 50,
    justifyContent: 'center',
    alignItems: 'center',
  },
  profileIcon: {
    position: 'relative',
    backgroundColor: '#E8F4FD',
    borderRadius: 25,
    width: 50,
    height: 50,
    justifyContent: 'center',
    alignItems: 'center',
  },
  idBadge: {
    position: 'absolute',
    bottom: -2,
    right: -2,
    backgroundColor: '#fff',
    borderRadius: 8,
    width: 16,
    height: 12,
    justifyContent: 'center',
    alignItems: 'center',
    borderWidth: 1,
    borderColor: '#ddd',
  },
  redSquare: {
    width: 6,
    height: 6,
    backgroundColor: '#E74C3C',
    borderRadius: 1,
  },
  heartHandIcon: {
    position: 'relative',
    width: 50,
    height: 50,
    justifyContent: 'center',
    alignItems: 'center',
  },
  handIcon: {
    position: 'absolute',
    bottom: 5,
    right: 8,
  },
  cardText: {
    fontSize: 18,
    fontWeight: '500',
    color: '#333',
  },
});

export default MenuBeneficiario;