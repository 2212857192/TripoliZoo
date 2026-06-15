package com.example.tripolizoo

import android.content.ContentValues
import android.media.MediaScannerConnection
import android.os.Build
import android.os.Environment
import android.provider.MediaStore
import io.flutter.embedding.engine.FlutterEngine
import io.flutter.embedding.android.FlutterActivity
import io.flutter.plugin.common.MethodChannel
import java.io.File
import java.io.FileOutputStream

class MainActivity : FlutterActivity() {
    private val channelName = "tripolizoo/ticket_images"

    override fun configureFlutterEngine(flutterEngine: FlutterEngine) {
        super.configureFlutterEngine(flutterEngine)

        MethodChannel(flutterEngine.dartExecutor.binaryMessenger, channelName)
            .setMethodCallHandler { call, result ->
                if (call.method != "saveImage") {
                    result.notImplemented()
                    return@setMethodCallHandler
                }

                val bytes = call.argument<ByteArray>("bytes")
                val name = call.argument<String>("name")
                if (bytes == null || name.isNullOrBlank()) {
                    result.error("invalid_image", "تعذر قراءة صورة التذكرة", null)
                    return@setMethodCallHandler
                }

                try {
                    result.success(saveImage(bytes, name))
                } catch (error: Exception) {
                    result.error("save_failed", error.localizedMessage, null)
                }
            }
    }

    private fun saveImage(bytes: ByteArray, name: String): Boolean {
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.Q) {
            val values = ContentValues().apply {
                put(MediaStore.Images.Media.DISPLAY_NAME, name)
                put(MediaStore.Images.Media.MIME_TYPE, "image/png")
                put(
                    MediaStore.Images.Media.RELATIVE_PATH,
                    "${Environment.DIRECTORY_PICTURES}/TripoliZoo",
                )
                put(MediaStore.Images.Media.IS_PENDING, 1)
            }
            val resolver = contentResolver
            val uri = resolver.insert(
                MediaStore.Images.Media.EXTERNAL_CONTENT_URI,
                values,
            ) ?: return false

            resolver.openOutputStream(uri)?.use { it.write(bytes) }
                ?: return false
            values.clear()
            values.put(MediaStore.Images.Media.IS_PENDING, 0)
            resolver.update(uri, values, null, null)
            return true
        }

        val pictures = Environment.getExternalStoragePublicDirectory(
            Environment.DIRECTORY_PICTURES,
        )
        val album = File(pictures, "TripoliZoo").apply { mkdirs() }
        val imageFile = File(album, name)
        FileOutputStream(imageFile).use { it.write(bytes) }
        MediaScannerConnection.scanFile(
            this,
            arrayOf(imageFile.absolutePath),
            arrayOf("image/png"),
            null,
        )
        return true
    }
}
