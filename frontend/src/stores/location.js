import { ref, computed, reactive } from "vue";
import { defineStore } from "pinia";

const getUserLocation = async () => {
  return new Promise((res, reject) => {
    navigator.geolocation.getCurrentPosition(res, reject);
  });
};

export const useLocationStore = defineStore("location", () => {
  const destination = reactive({
    name: "",
    address: "",
    geometry: {
      lat: null,
      lng: null,
    },
  });

  const currentLocation = reactive({
    geometry: {
      lat: null,
      lng: null,
    },
  });

  const updateCurrentLocation = async () => {
    const userLocation = await getUserLocation();

    currentLocation.geometry = {
      lat: userLocation.coords.latitude,
      lng: userLocation.coords.longitude,
    };
  };
  return { destination, currentLocation, updateCurrentLocation };
});
