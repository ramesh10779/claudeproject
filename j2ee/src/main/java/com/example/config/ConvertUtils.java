package com.example.config;

import java.util.List;
import java.util.Map;
import java.util.stream.Collectors;

public class ConvertUtils {
    public static String xmlEscapeAttr(String s) {
        if (s == null) return "";
        return s
                .replace("&", "&amp;")
                .replace("<", "&lt;")
                .replace(">", "&gt;")
                .replace("\"", "&quot;")
                .replace("'", "&apos;");
    }

    public static String toXml(List<String> envOrder, Map<String, Map<String, String>> envConfigs) {
        StringBuilder sb = new StringBuilder();
        sb.append("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n");
        sb.append("<environments>\n");
        for (String env : envOrder) {
            Map<String, String> kv = envConfigs.get(env);
            sb.append("  <environment name=\"").append(xmlEscapeAttr(env)).append("\">\n");
            if (kv != null) {
                for (Map.Entry<String, String> e : kv.entrySet()) {
                    sb.append("    <pair key=\"")
                      .append(xmlEscapeAttr(e.getKey()))
                      .append("\" value=\"")
                      .append(xmlEscapeAttr(e.getValue()))
                      .append("\"/>")
                      .append("\n");
                }
            }
            sb.append("  </environment>\n");
        }
        sb.append("</environments>\n");
        return sb.toString();
    }

    public static String toYaml(List<String> envOrder, Map<String, Map<String, String>> envConfigs) {
        StringBuilder sb = new StringBuilder();
        for (String env : envOrder) {
            sb.append(env).append(":\n");
            Map<String, String> kv = envConfigs.get(env);
            if (kv != null) {
                for (Map.Entry<String, String> e : kv.entrySet()) {
                    sb.append("  ")
                      .append(e.getKey())
                      .append(": ")
                      .append(quoteIfNeeded(e.getValue()))
                      .append("\n");
                }
            }
            sb.append("\n");
        }
        return sb.toString();
    }

    private static String quoteIfNeeded(String v) {
        if (v == null) return "";
        boolean needsQuotes = v.contains(":") || v.contains("#") || v.contains("\n") || v.startsWith(" ") || v.endsWith(" ") || v.contains("\"");
        if (needsQuotes) {
            return '"' + v.replace("\"", "\\\"") + '"';
        }
        return v;
    }
}