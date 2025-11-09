package com.example.config;

import com.google.gson.Gson;
import com.google.gson.reflect.TypeToken;

import javax.servlet.ServletException;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import java.io.BufferedReader;
import java.io.IOException;
import java.io.PrintWriter;
import java.lang.reflect.Type;
import java.nio.charset.StandardCharsets;
import java.util.List;
import java.util.Map;

public class ExportYamlServlet extends HttpServlet {
    private final Gson gson = new Gson();

    @Override
    protected void doPost(HttpServletRequest req, HttpServletResponse resp) throws ServletException, IOException {
        StringBuilder body = new StringBuilder();
        try (BufferedReader reader = req.getReader()) {
            String line;
            while ((line = reader.readLine()) != null) {
                body.append(line);
            }
        }

        Type payloadType = new TypeToken<Map<String, Object>>() {}.getType();
        Map<String, Object> payload = gson.fromJson(body.toString(), payloadType);

        @SuppressWarnings("unchecked")
        List<String> envOrder = (List<String>) payload.getOrDefault("envOrder", List.of());
        @SuppressWarnings("unchecked")
        Map<String, Map<String, String>> envConfigs = (Map<String, Map<String, String>>) payload.get("envConfigs");
        if (envConfigs == null) {
            envConfigs = Map.of();
        }

        String yaml = ConvertUtils.toYaml(envOrder, envConfigs);

        resp.setCharacterEncoding(StandardCharsets.UTF_8.name());
        resp.setContentType("application/x-yaml");
        resp.setHeader("Content-Disposition", "attachment; filename=\"config.yaml\"");
        try (PrintWriter out = resp.getWriter()) {
            out.write(yaml);
        }
    }
}