const path = require('path');
const VueLoaderPlugin = require('vue-loader/lib/plugin');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');

module.exports = {
    mode: 'production',
    entry: './resources/scripts/app.js',
    output: {
        path: path.resolve(__dirname, 'resources/scripts/dist'),
        filename: 'app.js'
    },
    resolve: {
        alias: {
            "~": path.resolve(__dirname, 'resources/scripts/src'),
            "vue$": 'vue/dist/vue.min.js'
        },
        modules: [path.resolve(__dirname, 'node_modules')],
        extensions: ['.js', '.vue'],
    },
    module: {
        rules: [
            {
                test: /\.vue$/,
                loader: 'vue-loader',
                options: {
                    hotReload: false
                }
            }, {
                test: /\.js$/,
                loader: 'babel-loader'
            },       {
                test: /\.css$/i,
                use: [MiniCssExtractPlugin.loader, 'css-loader']
            }
        ]
    },
    plugins: [
        new VueLoaderPlugin(),
        new MiniCssExtractPlugin({
            filename: 'app.css'
        })
    ]
};