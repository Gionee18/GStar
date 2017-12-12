package com.gionee.gioneeabc.bean;

import java.util.ArrayList;

/**
 * Created by root on 24/10/16.
 */
public class ZoneBean {
    private int count;
    private String status;
    public ArrayList<Data> data;

    public int getCount() {
        return count;
    }

    public void setCount(int count) {
        this.count = count;
    }

    public String getStatus() {
        return status;
    }

    public void setStatus(String status) {
        this.status = status;
    }

    public ArrayList<Data> getData() {
        return data;
    }

    public void setData(ArrayList<Data> data) {
        this.data = data;
    }

    public class Data {
        private int id;
        private String zone_name;

        public int getId() {
            return id;
        }

        public void setId(int id) {
            this.id = id;
        }

        public String getZone_name() {
            return zone_name;
        }

        public void setZone_name(String zone_name) {
            this.zone_name = zone_name;
        }
    }
}
