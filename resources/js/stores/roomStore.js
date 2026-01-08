import { defineStore } from 'pinia';
import axios from 'axios';

export const useRoomStore = defineStore('rooms', {
    state: () => ({
        floors: [],
        isLoading: false,
        lastUpdated: null,
        filterType: 'all',
        sortByPrice: null,
        overtimeRate: 0,
        currentTime: new Date(),
    }),

    getters: {
        allRooms: (state) => {
            return state.floors.flatMap(floor => floor.rooms);
        },
        filteredRooms: (state) => {
            let rooms = state.allRooms;
            if (state.filterType !== 'all') {
                rooms = rooms.filter(room => room.type === state.filterType);
            }
            if (state.sortByPrice === 'desc') {
                rooms = [...rooms].sort((a, b) => b.price - a.price);
            } else if (state.sortByPrice === 'asc') {
                rooms = [...rooms].sort((a, b) => a.price - b.price);
            }
            return rooms;
        }
    },

    actions: {
        async fetchDashboardData() {
            this.isLoading = true;
            try {
                const response = await axios.get('/api/dashboard/data');
                this.floors = response.data.floors;
                this.overtimeRate = response.data.overtime_rate;
                this.lastUpdated = new Date();
            } catch (error) {
                console.error('Error fetching dashboard data:', error);
            } finally {
                this.isLoading = false;
            }
        },

        async updateRoomStatus() {
            try {
                const response = await axios.get('/api/rooms/status');
                const roomStatuses = response.data;

                // Update room statuses in the local state
                this.floors.forEach(floor => {
                    floor.rooms.forEach(room => {
                        const updatedRoom = roomStatuses.find(r => r.id === room.id);
                        if (updatedRoom) {
                            room.status = updatedRoom.status;
                            room.active_reservation = updatedRoom.reservation;
                        }
                    });
                });
                this.lastUpdated = new Date();
            } catch (error) {
                console.error('Error updating room statuses:', error);
            }
        },

        setFilterType(type) {
            this.filterType = type;
        },

        toggleSortByPrice() {
            if (this.sortByPrice === 'desc') {
                this.sortByPrice = null;
            } else {
                this.sortByPrice = 'desc';
            }
        },

        tick() {
            this.currentTime = new Date();
        }
    }
});
