# Use the specific Node.js version
FROM node:22.2.0 as build-stage

# Update npm to the latest version
RUN npm install -g npm@10.8.1

# Set the working directory
WORKDIR /app

# Copy package.json and package-lock.json
COPY package.json package-lock.json ./

# Install dependencies
RUN npm install

# Copy the rest of the application code
COPY . .

# Debugging: List files in /app/plugins/iconify
RUN ls -al /app/plugins/iconify

# Expose port 3000
EXPOSE 3000

# Clean up temporary files before starting the app
RUN rm -rf /tmp/nitro/worker-*

# Command to start the Nuxt.js app in development mode
CMD ["npm", "run", "dev"]
