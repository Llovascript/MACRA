import React, { useState } from 'react';
import {
  View,
  Text,
  TouchableOpacity,
  StyleSheet,
  SafeAreaView,
  ScrollView,
  FlatList,
} from 'react-native';
import Icon from 'react-native-vector-icons/MaterialIcons';

const EventosDisponibles = ({ navigation }) => {
  const [currentPage, setCurrentPage] = useState(1);
  const [totalPages] = useState(5); // Ejemplo: 5 páginas totales

  // Datos de ejemplo para los eventos
  const [eventos] = useState([
    {
      id: 1,
      nombre: 'Evento 1',
      fechaInicio: '2024-01-15',
      fechaTermino: '2024-01-16',
    },
    {
      id: 2,
      nombre: 'Evento 2',
      fechaInicio: '2024-01-20',
      fechaTermino: '2024-01-21',
    },
    {
      id: 3,
      nombre: 'Evento 3',
      fechaInicio: '2024-01-25',
      fechaTermino: '2024-01-26',
    },
    {
      id: 4,
      nombre: 'Evento 4',
      fechaInicio: '2024-02-01',
      fechaTermino: '2024-02-02',
    },
    {
      id: 5,
      nombre: 'Evento 5',
      fechaInicio: '2024-02-05',
      fechaTermino: '2024-02-06',
    },
  ]);

  const handleApply = (eventoId) => {
    console.log('Aplicando a evento:', eventoId);
    // Lógica para aplicar al evento
  };

  const handleAdd = () => {
    console.log('Agregando nuevo evento...');
    // Lógica para agregar nuevo evento
  };

  const handlePreviousPage = () => {
    if (currentPage > 1) {
      setCurrentPage(currentPage - 1);
    }
  };

  const handleNextPage = () => {
    if (currentPage < totalPages) {
      setCurrentPage(currentPage + 1);
    }
  };

  const renderEventRow = ({ item }) => (
    <View style={styles.tableRow}>
      <View style={styles.tableCell}>
        <Text style={styles.cellText}>{item.nombre}</Text>
      </View>
      <View style={styles.tableCell}>
        <Text style={styles.cellText}>{item.fechaInicio}</Text>
      </View>
      <View style={styles.tableCell}>
        <Text style={styles.cellText}>{item.fechaTermino}</Text>
      </View>
      <View style={styles.actionCell}>
        <TouchableOpacity 
          style={styles.applyButton}
          onPress={() => handleApply(item.id)}
        >
          <Text style={styles.applyButtonText}>Aplicar</Text>
        </TouchableOpacity>
      </View>
    </View>
  );

  return (
    <SafeAreaView style={styles.container}>
      <View style={styles.header}>
        <TouchableOpacity 
          style={styles.backButton}
          onPress={() => navigation.goBack()}
        >
          <Icon name="arrow-back" size={24} color="#000" />
        </TouchableOpacity>
        <Text style={styles.title}>Eventos{'\n'}disponibles</Text>
      </View>

      <View style={styles.content}>
        {/* Table Header */}
        <View style={styles.tableHeader}>
          <View style={styles.headerCell}>
            <Text style={styles.headerText}>Nombre</Text>
          </View>
          <View style={styles.headerCell}>
            <Text style={styles.headerText}>Fecha inicio</Text>
          </View>
          <View style={styles.headerCell}>
            <Text style={styles.headerText}>Fecha término</Text>
          </View>
          <View style={styles.headerCell}>
            <Text style={styles.headerText}>Acción</Text>
          </View>
        </View>

        {/* Table Content */}
        <View style={styles.tableContainer}>
          <FlatList
            data={eventos}
            renderItem={renderEventRow}
            keyExtractor={(item) => item.id.toString()}
            showsVerticalScrollIndicator={false}
          />
        </View>

        {/* Pagination */}
        <View style={styles.paginationContainer}>
          <TouchableOpacity 
            style={styles.paginationButton}
            onPress={handlePreviousPage}
            disabled={currentPage === 1}
          >
            <Icon 
              name="chevron-left" 
              size={20} 
              color={currentPage === 1 ? '#ccc' : '#666'} 
            />
          </TouchableOpacity>
          
          <Text style={styles.paginationText}>
            Página {currentPage} de {totalPages}
          </Text>
          
          <TouchableOpacity 
            style={styles.paginationButton}
            onPress={handleNextPage}
            disabled={currentPage === totalPages}
          >
            <Icon 
              name="chevron-right" 
              size={20} 
              color={currentPage === totalPages ? '#ccc' : '#666'} 
            />
          </TouchableOpacity>
        </View>

        {/* Add Button */}
        <View style={styles.addButtonContainer}>
          <TouchableOpacity style={styles.addButton} onPress={handleAdd}>
            <Text style={styles.addButtonText}>Agregar</Text>
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
    lineHeight: 22,
  },
  content: {
    flex: 1,
    margin: 16,
    backgroundColor: '#fff',
    borderRadius: 8,
    padding: 16,
  },
  tableHeader: {
    flexDirection: 'row',
    backgroundColor: '#e0e0e0',
    paddingVertical: 12,
    paddingHorizontal: 8,
    borderRadius: 4,
    marginBottom: 2,
  },
  headerCell: {
    flex: 1,
    alignItems: 'center',
  },
  headerText: {
    fontSize: 12,
    fontWeight: '500',
    color: '#333',
    textAlign: 'center',
  },
  tableContainer: {
    flex: 1,
    backgroundColor: '#f8f8f8',
  },
  tableRow: {
    flexDirection: 'row',
    backgroundColor: '#fff',
    paddingVertical: 12,
    paddingHorizontal: 8,
    borderBottomWidth: 1,
    borderBottomColor: '#e0e0e0',
    alignItems: 'center',
  },
  tableCell: {
    flex: 1,
    alignItems: 'center',
  },
  actionCell: {
    flex: 1,
    alignItems: 'center',
  },
  cellText: {
    fontSize: 12,
    color: '#333',
    textAlign: 'center',
  },
  applyButton: {
    backgroundColor: '#ff9800',
    paddingHorizontal: 12,
    paddingVertical: 6,
    borderRadius: 12,
  },
  applyButtonText: {
    color: '#fff',
    fontSize: 10,
    fontWeight: '500',
  },
  paginationContainer: {
    flexDirection: 'row',
    justifyContent: 'center',
    alignItems: 'center',
    paddingVertical: 16,
    borderTopWidth: 1,
    borderTopColor: '#e0e0e0',
  },
  paginationButton: {
    padding: 8,
  },
  paginationText: {
    fontSize: 14,
    color: '#666',
    marginHorizontal: 16,
  },
  addButtonContainer: {
    alignItems: 'center',
    paddingTop: 16,
  },
  addButton: {
    backgroundColor: '#4caf50',
    paddingHorizontal: 32,
    paddingVertical: 12,
    borderRadius: 20,
    minWidth: 120,
  },
  addButtonText: {
    color: '#fff',
    fontSize: 14,
    fontWeight: '500',
    textAlign: 'center',
  },
});

export default EventosBeneficiario;