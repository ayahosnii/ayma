# Project Features Overview

This document provides an overview of the key features implemented in the project.

## 1. Order Tracking Map
- **URL:** [http://localhost:3000/orders/trackmap](http://localhost:3000/orders/trackmap)
- **Description:** A real-time map that allows users to track the delivery location of their orders.
- **Key Components:**
    - **Socket.io Integration:** Real-time updates of delivery location via Socket.io.
    - **Google Maps Integration:** Displays the delivery location on a map using Google Maps API.
    - **Automatic Location Updates:** The map updates automatically when the location changes.

## 2. Real-Time Location Updates
- **Socket Channel:** `laravel_database_tracking.<delivery_id>`
- **Description:** The delivery location is published to a Redis channel, and any updates are broadcasted to the client.
- **Key Components:**
    - **Redis Integration:** Data is published to a Redis channel whenever the delivery location is updated.
    - **Real-Time Updates:** The client listens for updates and reflects the changes on the map.

## 3. API for Delivery Data
- **Endpoint:** `/deliveries/{id}`
- **Description:** Provides the current latitude and longitude of the delivery in JSON format.
- **Key Components:**
    - **Axios Integration:** Fetches location data from the backend using Axios.
    - **Authentication:** Requires Bearer token for access.

## 4. Google Maps Customization
- **Zoom Level:** The map is initially zoomed at a level of `8`, but this can be adjusted based on the real-time location.
- **Marker Update:** A new marker is placed on the map each time the location updates.

## 5. Future Enhancements
- **User Authentication:** Integrating user authentication for personalized delivery tracking.
- **Route Tracking:** Showing the delivery route on the map based on the tracked locations.

## Setup Instructions
1. Clone the repository.
2. Install dependencies with `npm install` or `yarn install`.
3. Run the development server with `npm run dev` or `yarn dev`.
4. Access the tracking map at `http://localhost:3000/orders/trackmap`.

## Conclusion
This feature enables real-time tracking of orders, providing a seamless experience for customers to monitor delivery progress via an interactive map.
