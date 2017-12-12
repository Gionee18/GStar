package com.gionee.gioneeabc.bean;

import java.io.Serializable;
import java.util.ArrayList;
import java.util.List;

/**
 * Created by Linchpin
 */
public class RecommAttribBean implements Serializable{
    private int count;

    public int getCount() {
        return this.count;
    }

    public void setCount(int count) {
        this.count = count;
    }

    private String status;

    public String getStatus() {
        return this.status;
    }

    public void setStatus(String status) {
        this.status = status;
    }

    private List<RecommAttribData> data=new ArrayList<>();

    public List<RecommAttribData> getData() {
        return data;
    }

    public void setData(List<RecommAttribData> data) {
        this.data = data;
    }

    public class RecommAttribData implements Serializable{
        private int id;
        private String name;
        private String id_key;
        private String value_key;
        private List<String> search_attribute=new ArrayList<>();
        private List<String> selSearchAttrib=new ArrayList<>();

        public int getId() {
            return id;
        }

        public void setId(int id) {
            this.id = id;
        }

        public String getName() {
            return name;
        }

        public void setName(String name) {
            this.name = name;
        }

        public List<String> getSearch_attribute() {
            return search_attribute;
        }

        public void setSearch_attribute(List<String> search_attribute) {
            this.search_attribute = search_attribute;
        }

        public List<String> getSelSearchAttrib() {
            return selSearchAttrib;
        }

        public void setSelSearchAttrib(List<String> selSearchAttrib) {
            this.selSearchAttrib = selSearchAttrib;
        }

        public String getId_key() {
            return id_key;
        }

        public void setId_key(String id_key) {
            this.id_key = id_key;
        }

        public String getValue_key() {
            return value_key;
        }

        public void setValue_key(String value_key) {
            this.value_key = value_key;
        }
    }
}
