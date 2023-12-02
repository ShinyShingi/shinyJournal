// imagePlugin.js
export default {
    install(app) {
        app.config.globalProperties.$getImageUrl = (imagePath) => {
            return `${import.meta.env.VITE_API_URL}/storage/app/public/${imagePath}`;
        };
    }
};
