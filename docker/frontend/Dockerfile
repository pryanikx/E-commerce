FROM node:18-alpine

# Устанавливаем необходимые пакеты для компиляции нативных модулей
RUN apk add --no-cache \
    python3 \
    make \
    g++ \
    libc6-compat

WORKDIR /app

# Копируем package.json и package-lock.json
COPY package*.json ./

# Очищаем npm cache и устанавливаем зависимости
RUN npm cache clean --force
RUN rm -rf node_modules package-lock.json
RUN npm install

# Копируем весь код
COPY . .

# Открываем порт для Vite dev server
EXPOSE 5173

# Запускаем dev server
CMD ["npm", "run", "dev", "--", "--host", "0.0.0.0"]