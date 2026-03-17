'use client';

import { useCartSync } from "../hooks/useCartSync";

export default function CartSyncHandler() {
    useCartSync();
    return null;
}
