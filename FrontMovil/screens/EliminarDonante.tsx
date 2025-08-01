import React, { useState } from 'react';
import {
  View,
  Text,
  TextInput,
  TouchableOpacity,
  StyleSheet,
  SafeAreaView,
  Alert,
} from 'react-native';
import Icon from 'react-native-vector-icons/MaterialIcons';

const EliminarDonante = ({ navigation }) => {
  const [formData, setFormData] = useState({
    campo1: '',
    campo2: '',
    campo3: '',
    campo4: '',
  });

  const handleInputChange = (field, value) => {
    setFormData(prev => ({
      ...prev,
      [field]: value
    }));
  };

  const handleDelete = () => {
    Alert.alert(
      'Confirmar eliminación',
      '¿Estás seguro de que deseas eliminar este donante?',
      [
        {
          text: 'Cancelar',
          style: 'cancel',
        },
        {
          text: 'Eliminar',
          style: 'destructive',
          onPress: () => {
            // Lógica para eliminar donante
            console.log('Eliminando donante:', formData);
          },
        },
      ]
    );
  };

  const handleSearch = () => {
    // Lógica para buscar donante
    console.log('Buscando donante...');
  };

  return (
    <SafeAreaView style={styles.container}>
      <View style={styles.header}>
        <TouchableOpacity 
          style={styles.backButton}
          onPress={() => navigation.goBack()}
        >
          <Icon name="arrow-back" size={24} color="#000" />
        </TouchableOpacity>
        <Text style={styles.title}>Eliminar donante</Text>
      </View>

      <View style={styles.formContainer}>
        <View style={styles.sectionHeader}>
          <Text style={styles.sectionTitle}>Información del Donante</Text>
        </View>

        <View style={styles.inputGroup}>
          <Text style={styles.inputLabel}>Texto</Text>
          <View style={styles.inputWithIcon}>
            <TextInput
              style={styles.textInputWithIcon}
              value={formData.campo1}
              onChangeText={(value) => handleInputChange('campo1', value)}
              placeholder=""
              editable={false}
            />
            <TouchableOpacity style={styles.searchIcon} onPress={handleSearch}>
              <Icon name="search" size={20} color="#2196f3" />
            </TouchableOpacity>
          </View>
        </View>

        <View style={styles.inputGroup}>
          <Text style={styles.inputLabel}>Texto</Text>
          <TextInput
            style={[styles.textInput, styles.disabledInput]}
            value={formData.campo2}
            onChangeText={(value) => handleInputChange('campo2', value)}
            placeholder=""
            editable={false}
          />
        </View>

        <View style={styles.inputGroup}>
          <Text style={styles.inputLabel}>Texto</Text>
          <TextInput
            style={[styles.textInput, styles.disabledInput]}
            value={formData.campo3}
            onChangeText={(value) => handleInputChange('campo3', value)}
            placeholder=""
            editable={false}
          />
        </View>

        <View style={styles.inputGroup}>
          <Text style={styles.inputLabel}>Texto</Text>
          <TextInput
            style={[styles.textInput, styles.disabledInput]}
            value={formData.campo4}
            onChangeText={(value) => handleInputChange('campo4', value)}
            placeholder=""
            editable={false}
          />
        </View>

        <View style={styles.buttonContainer}>
          <TouchableOpacity style={styles.deleteButton} onPress={handleDelete}>
            <Text style={styles.deleteButtonText}>Eliminar</Text>
          </TouchableOpacity>
        </View>
      </View>
    </SafeAreaView>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#f5f5f5',
  },
  header: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingHorizontal: 16,
    paddingVertical: 12,
    backgroundColor: '#fff',
  },
  backButton: {
    marginRight: 16,
  },
  title: {
    fontSize: 18,
    fontWeight: '500',
    color: '#000',
  },
  formContainer: {
    flex: 1,
    margin: 16,
    backgroundColor: '#fff',
    borderRadius: 8,
    padding: 16,
  },
  sectionHeader: {
    backgroundColor: '#e0e0e0',
    padding: 12,
    marginBottom: 16,
    borderRadius: 4,
  },
  sectionTitle: {
    fontSize: 14,
    fontWeight: '500',
    color: '#333',
  },
  inputGroup: {
    marginBottom: 16,
  },
  inputLabel: {
    fontSize: 12,
    color: '#666',
    marginBottom: 4,
  },
  textInput: {
    borderWidth: 1,
    borderColor: '#ccc',
    borderRadius: 4,
    padding: 12,
    fontSize: 14,
    backgroundColor: '#fff',
  },
  disabledInput: {
    backgroundColor: '#f5f5f5',
    color: '#999',
  },
  inputWithIcon: {
    flexDirection: 'row',
    alignItems: 'center',
    borderWidth: 1,
    borderColor: '#ccc',
    borderRadius: 4,
    backgroundColor: '#f5f5f5',
  },
  textInputWithIcon: {
    flex: 1,
    padding: 12,
    fontSize: 14,
    color: '#999',
  },
  searchIcon: {
    padding: 12,
  },
  buttonContainer: {
    alignItems: 'center',
    marginTop: 32,
  },
  deleteButton: {
    backgroundColor: '#f44336',
    paddingHorizontal: 32,
    paddingVertical: 12,
    borderRadius: 20,
    minWidth: 120,
  },
  deleteButtonText: {
    color: '#fff',
    fontSize: 14,
    fontWeight: '500',
    textAlign: 'center',
  },
});

export default EliminarDonante;