# Project Features Overview

This document provides an overview of the key features implemented in the project.

### 1- Integrated Google Maps to Locate the Order Easily
- Google Maps has been integrated to display the current location of the order on an interactive map.
### 2- Real-Time Order Tracking and Delivery Location Updates

This feature allows users to track their orders in real-time with an interactive map. It integrates **Google Maps** and **Socket.io** for live location updates, and the data is fetched via an API and broadcasted through a **Redis** channel. The map automatically updates with a dynamic marker showing the latest location.

- Access the tracking map at: [http://localhost:3000/orders/trackmap](http://localhost:3000/orders/trackmap).
