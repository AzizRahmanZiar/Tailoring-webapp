import { PostProvider } from '@/Contexts/PostContext';
import { RatingProvider } from '@/Contexts/RatingContext';
import { ToastProvider } from '@/Contexts/ToastContext';
import { RegProvider } from "@/Contexts/RegContext";
import { ClothsProvider } from "@/Contexts/ClothsContext";
import { UniformProvider } from "@/Contexts/UniformContext";
import { KortaiProvider } from "@/Contexts/KortaiContext";
import { SadraiProvider } from "@/Contexts/SadraiContext";

const GlobalProviders = ({ children }) => {
    return (
        <ToastProvider>
            <PostProvider>
                <RatingProvider>
                    <RegProvider>
                        <ClothsProvider>
                            <UniformProvider>
                                <KortaiProvider>
                                    <SadraiProvider>
                                        {children}
                                    </SadraiProvider>
                                </KortaiProvider>
                            </UniformProvider>
                        </ClothsProvider>
                    </RegProvider>
                </RatingProvider>
            </PostProvider>
        </ToastProvider>
    );
};

export default GlobalProviders; 