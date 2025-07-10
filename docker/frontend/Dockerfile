FROM node:18-alpine

RUN apk add --no-cache \
    python3 \
    make \
    g++ \
    libc6-compat

WORKDIR /app

COPY package*.json ./

RUN npm cache clean --force
RUN rm -rf node_modules package-lock.json
RUN npm install

COPY . .

EXPOSE 5173

CMD ["npm", "run", "dev", "--", "--host", "0.0.0.0"]